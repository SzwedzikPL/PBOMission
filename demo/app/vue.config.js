process.env.VUE_APP_UPLOADER_PATH = process.env.NODE_ENV === 'production' ? '' : '../../';

module.exports = {
  publicPath: '',
  pages: {
    index: {
      entry: 'src/main.js',
      template: 'public/index.php',
      filename: 'index.php'
    }
  },
  indexPath: 'index.php'
}
