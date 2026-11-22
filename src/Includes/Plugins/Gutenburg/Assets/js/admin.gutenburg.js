( () => {
	'use strict';

	if (
		! window.wp ||
		! window.wp.plugins ||
		! window.wp.editPost ||
		! window.wp.element ||
		! window.wp.components ||
		! window.wp.data ||
		! window.wp.coreData
	) {
		return;
	}

	const { registerPlugin } = window.wp.plugins;
	const { PluginDocumentSettingPanel } = window.wp.editPost;
	const MainDashboardButton = window.wp.editPost.__experimentalMainDashboardButton;
	const FullscreenModeClose = window.wp.editPost.__experimentalFullscreenModeClose;
	const { SelectControl, ToggleControl, TextControl } = window.wp.components;
	const { createElement: el } = window.wp.element;
	const { useSelect, useDispatch } = window.wp.data;
	const { useEntityProp } = window.wp.coreData;
	const translate = typeof window.wp.i18n?.__ === 'function'
		? window.wp.i18n.__
		: ( text ) => text;

	const config = window.wikipressGutenbergWikiPage || {};
	const metaKey = config.metaKey || '_wikipress_permalink';
	const wikiOptions = [
		{ label: translate( 'Select a wiki', 'wikipress' ), value: '' },
		...( config.wikis || [] ),
	];

	const pagesUrl = config.pagesUrl || '';

	const WikiPageBackButton = () => {
		if (!MainDashboardButton || !FullscreenModeClose || !pagesUrl) {
			return null;
		}

		const postType = useSelect(
			( select ) => select( 'core/editor' ).getCurrentPostType(),
			[]
		);

		if ('wikipress_wiki_page' !== postType) {
			return null;
		}

		const parentId = useSelect(
			( select ) => select( 'core/editor' ).getEditedPostAttribute( 'parent' ),
			[]
		);
		const selectedParentId = parentId || config.parentId || 0;
		const url = new URL(pagesUrl, window.location.origin);

		if (selectedParentId) {
			url.searchParams.set('wiki', String(selectedParentId));
		}

		return el(
			MainDashboardButton,
			null,
			el(FullscreenModeClose, {
				href: url.toString(),
			})
		);
	};

	registerPlugin( 'wikipress-wiki-page-back-button', {
		render: WikiPageBackButton,
	} );

	const WikiPageSettingsPanel = () => {
		const postType = useSelect(
			( select ) => select( 'core/editor' ).getCurrentPostType(),
			[]
		);

		if ( 'wikipress_wiki_page' !== postType ) {
			return null;
		}

		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
		const parentId = useSelect(
			( select ) => select( 'core/editor' ).getEditedPostAttribute( 'parent' ),
			[]
		);
		const selectedParentId = parentId || config.parentId || 0;
		const { editPost } = useDispatch( 'core/editor' );
		const permalinkOverride = typeof meta?.[ metaKey ] === 'string'
			? meta[ metaKey ]
			: '';

		const setPermalinkOverride = ( value ) => {
			setMeta( {
				...meta,
				[ metaKey ]: value,
			} );
		};

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'wikipress-wiki-page-settings',
				title: translate( 'Wiki Page Settings', 'wikipress' ),
			},
			el( SelectControl, {
				label: translate( 'Parent Wiki', 'wikipress' ),
				value: String( selectedParentId || '' ),
				options: wikiOptions,
				onChange: ( value ) => editPost( { parent: value ? Number( value ) : 0 } ),
			} ),
			el( ToggleControl, {
				label: translate( 'Use a custom permalink', 'wikipress' ),
				checked: '' !== permalinkOverride,
				onChange: ( enabled ) => setPermalinkOverride(
					enabled
						? ( permalinkOverride || '%wiki_root%/%wiki_parent%/%wiki_page%' )
						: ''
				),
			} ),
			'' !== permalinkOverride
				? el( TextControl, {
					label: translate( 'Custom Permalink', 'wikipress' ),
					value: permalinkOverride,
					onChange: setPermalinkOverride,
					help: translate(
						'Use tokens such as %wiki_root%, %wiki_parent%, %wiki_category%, %wiki_tag%, or %wiki_page%.',
						'wikipress'
					),
				} )
				: null
		);
	};

	registerPlugin( 'wikipress-wiki-page-settings', {
		render: WikiPageSettingsPanel,
	} );
} )();
