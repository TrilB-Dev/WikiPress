/**
  * Mod Manager JS
  *
  * @package: ModPress
  * @subpackage: Admin\Manager\Mods
  * @version 1.0.0
  * @since 1.0.0
  */
(() => {
    /**
     * Global / shared script setup
     *
     * These helpers are reused across the Mod Manager and should remain outside
     * page-specific blocks so shared logic is easy to find.
     * @since 1.0.0
     */
    const wikiManagerRoot = document.querySelector('.wikipress-wiki-manager-tab-content');
    wikiManagerRoot && (() => {

    })();
    /**
     * Wikis page script setup
     *
     * These helpers are specific to the Wikis page.
     * Contains page-specific logic for managing wikis.
     *
     * @since 1.0.0
     */
    const wikiPage = document.querySelector('.wikipress-wikis-page');
    wikiPage && (() => {
        wikiPage.querySelectorAll('.modal[id^="wikipress-wiki-modal"]').forEach((wikiModal) => {
            const permalinkOverride = wikiModal.querySelector('[name="wikipress_wiki[settings][permalink_override]"]');
            const permalinkField = wikiModal.querySelector('[name="wikipress_wiki[settings][permalink]"]');
            const permalinkRow = permalinkField?.closest('tr');

            const updatePermalinkVisibility = () => {
                if (!permalinkRow) {
                    return;
                }

                permalinkRow.hidden = !(permalinkOverride?.checked);
            };

            permalinkOverride?.addEventListener('change', updatePermalinkVisibility);
            updatePermalinkVisibility();
        });

        /**
         * Initializes and handles the actions modal for wiki management, including delete confirmations.
         * @since 1.0.0
         */
        const modal = wikiPage.querySelector('#wikipress-actions-modal');
        if (!modal) {
            return;
        }

        const form = modal.querySelector('#wikipress-actions-form');
        const actionTitle = modal.querySelector('[data-actions-modal-title]');
        const actionBody = modal.querySelector('[data-actions-modal-body]');
        const actionInput = form?.querySelector('input[name="action"]');
        const nonceInput = form?.querySelector('input[name="wikipress_wiki_nonce"]');
        const wikiIdInput = form?.querySelector('input[name="wikipress_wiki_id"]');
        const wikiPageIdInput = form?.querySelector('input[name="wikipress_wiki_page_id"]');
        const pagesConfirmationWrap = modal.querySelector('[data-actions-pages-confirmation]');
        const pagesConfirmation = modal.querySelector('#confirm_delete_wiki_pages');

        modal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.dataset) {
                return;
            }

            const action = trigger.dataset.wikipressAction;
            const wikiId = trigger.dataset.wikiId ?? '';
            const wikiPageId = trigger.dataset.wikiPageId ?? '';
            const nonce = trigger.dataset.wikiNonce ?? '';
            const wikiTitle = trigger.dataset.wikiTitle ?? '';
            const wikiPageTitle = trigger.dataset.wikiPageTitle ?? '';
            const pagesCount = Number.parseInt(trigger.dataset.wikiPagesCount ?? '0', 10);

            if (pagesConfirmation) {
                pagesConfirmation.checked = true;
            }

            wikiIdInput && (wikiIdInput.value = wikiId);
            wikiPageIdInput && (wikiPageIdInput.value = wikiPageId);
            nonceInput && (nonceInput.value = nonce);

            if ('delete-wiki' === action) {
                actionInput && (actionInput.value = 'wikipress_delete_wiki');
                actionTitle && (actionTitle.textContent = `Delete Wiki: ${wikiTitle ? ` ${wikiTitle}` : ''}`);
                actionBody && (actionBody.textContent = 'Are you sure you want to delete this wiki?');
                pagesConfirmationWrap && (pagesConfirmationWrap.hidden = !(pagesCount > 0));
                modal.querySelector('[data-actions-wiki-confirmation]')?.removeAttribute('hidden');
            } else if ('delete-wiki-page' === action) {
                actionInput && (actionInput.value = 'wikipress_delete_wiki_page');
                actionTitle && (actionTitle.textContent = `Delete Wiki Page: ${wikiPageTitle ? ` ${wikiPageTitle}` : ''}`);
                actionBody && (actionBody.textContent = 'Are you sure you want to delete this wiki page?');
                pagesConfirmationWrap && (pagesConfirmationWrap.hidden = true);
                modal.querySelector('[data-actions-wiki-confirmation]')?.setAttribute('hidden', '');
            }
        });
    })();
    /**
     * Wiki Categories page script setup
     *
     * @since 1.0.0
     */
    const wikiCategoriesPage = document.querySelector('.wikipress-wiki-categories-page');
    wikiCategoriesPage && (() => {
    /**
     * Handles the behavior and interactions for the Wiki Categories page.
     *
     * @since 1.0.0
     */
    })();
    const wikiTagsPage = document.querySelector('.wikipress-wiki-tags-page');
    wikiTagsPage && (() => {

    })();

})();