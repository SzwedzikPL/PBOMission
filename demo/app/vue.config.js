process.env.VUE_APP_UPLOADER_PATH = process.env.NODE_ENV === 'production' ? '/' : '../../';

module.exports = {
  publicPath: process.env.NODE_ENV === 'production' ? '/' : ''
}
