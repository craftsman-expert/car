module.exports = {
  devServer: {
    host: '0.0.0.0',
    public: '0.0.0.0:3000',
    disableHostCheck: true
  },

  lintOnSave: false,
  pluginOptions: {
    quasar: {
      importStrategy: 'kebab',
      rtlSupport: false
    },
    i18n: {
      locale: 'ru',
      fallbackLocale: 'ru',
      localeDir: 'locales',
      enableInSFC: true
    }
  },
  transpileDependencies: [
    'quasar'
  ]
}
