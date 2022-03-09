module.exports = {
  lintOnSave: false,
  pwa: {
    name: 'Rough Budget',
    themeColor: '#151515',
    msTileColor: '#151515',
    workboxPluginMode: 'GenerateSW',
    workboxOptions: {
      exclude: [new RegExp('api/*.*')],
    }
  }
}
