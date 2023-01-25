const Path = require('path');
const { JavascriptWebpackConfig, CssWebpackConfig } = require('@silverstripe/webpack-config');

const PATHS = {
    ROOT: Path.resolve(),
    SRC: Path.resolve('client/src'),
    DIST: Path.resolve('client/dist'),
};

// Main JS bundle
const jsConfig = new JavascriptWebpackConfig('js', PATHS, 'symbiote/silverstripe-multivaluefield')
  .setEntry({
    multivaluefield: `${PATHS.SRC}/js/multivaluefield.js`
  })
  .getConfig();
// Tell webpack that jquery is externally accessible, but don't include default externals as this can be used on the frontend
jsConfig.externals = {
  jquery: 'jQuery'
};

const config = [
  jsConfig,
  // sass to css
  new CssWebpackConfig('css', PATHS)
    .setEntry({
      multivaluefield: `${PATHS.SRC}/styles/multivaluefield.scss`
    })
    .getConfig(),
];

// Use WEBPACK_CHILD=js or WEBPACK_CHILD=css env var to run a single config
module.exports = (process.env.WEBPACK_CHILD)
  ? config.find((entry) => entry.name === process.env.WEBPACK_CHILD)
  : config;
