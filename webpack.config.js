const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const resolveExistingPath = (...candidates) => {
  for (const candidate of candidates) {
    if (fs.existsSync(path.resolve(__dirname, candidate))) {
      return candidate;
    }
  }

  return candidates[0];
};

const pluginAssetPath = (pluginName, relativePath) => resolveExistingPath(
  `./src/Includes/Plugins/${pluginName}/Assets/${relativePath}`,
  `./src/includes/Plugins/${pluginName}/Assets/${relativePath}`
);

const entries = {
  bootstrap: [
    './src/Assets/js/bootstrap.js',
    './src/Assets/scss/bootstrap.scss',
  ],
  'admin.ui': [
    './src/Assets/js/admin.ui.js',
    './src/Assets/scss/admin.ui.scss',
  ],
  'wpoverride': './src/Assets/scss/wpoverride.scss',
  'bootstrap-select': [
    './src/Assets/js/bootstrap-select.js',
    './src/Assets/scss/bootstrap-select.scss',
  ]
};

const internalWikiEntries = {
  'admin.internal-wiki': [
    pluginAssetPath('InternalWiki', 'js/admin.internal-wiki.js'),
  ],
  'internal-wiki': [
    pluginAssetPath('InternalWiki', 'scss/internal-wiki.scss'),
    pluginAssetPath('InternalWiki', 'js/internal-wiki.js')
  ],
};

const fontAwesomeEntries = {
  'icon-picker': [
    pluginAssetPath('FontAwesome', 'js/icon-picker.js'),
    pluginAssetPath('FontAwesome', 'scss/icon-picker.scss'),
  ],
};

const tinyMCEEntries = {
  'tiny-mce': [
    pluginAssetPath('TinyMCE', 'js/tinymce.js')
  ],
};

const elementorEntries = {
  wiki: [
    pluginAssetPath('Elementor', 'js/WikiPress/wiki.js'),
    pluginAssetPath('Elementor', 'scss/WikiPress/wiki.scss'),
  ],
};

const gutenburgEntries = {
  blocks: pluginAssetPath('Gutenburg', 'js/blocks.js'),
};

const userRolesManagerEntries = {
  'user-roles-manager': [
    pluginAssetPath('UserRolesManager', 'js/user-roles-manager.js'),
    pluginAssetPath('UserRolesManager', 'scss/user-roles-manager.scss'),
  ],
};

const jsDirectory = path.resolve(__dirname, 'src/Assets/js');
fs.readdirSync(jsDirectory)
  .filter((file) => /^admin\.[^.]+\.js$/.test(file) && file !== 'admin.ui.js')
  .forEach((file) => {
    const page = file.match(/^admin\.([^.]+)\.js$/)[1];
    const entry = [`./src/Assets/js/${file}`];
    entries[`admin.${page}`] = entry;
  });

const shared = {
  mode: process.env.NODE_ENV === 'development' ? 'development' : 'production',
  devtool: process.env.NODE_ENV === 'development' ? 'source-map' : false,
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'sass-loader',
            options: {
              api: 'modern',
              sassOptions: {
                quietDeps: true,
                includePaths: [path.resolve(__dirname, 'src/Assets/scss')],
              },
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        type: 'javascript/auto',
      },
    ],
  },
  optimization: { splitChunks: false },
};

module.exports = [
  {
    ...shared,
    entry: entries,
    output: {
      path: path.resolve(__dirname, 'src/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: fontAwesomeEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/FontAwesome/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: elementorEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/Elementor/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: gutenburgEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/Gutenburg/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: userRolesManagerEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/UserRolesManager/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: internalWikiEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/InternalWiki/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  },
  {
    ...shared,
    entry: tinyMCEEntries,
    output: {
      path: path.resolve(__dirname, 'src/Includes/Plugins/TinyMCE/Assets/dist'),
      filename: 'js/[name].js',
      clean: true,
    },
    plugins: [
      new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
    ],
  }
];
