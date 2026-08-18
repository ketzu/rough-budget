import 'vuetify/styles'
import { createVuetify } from 'vuetify'

export default createVuetify({
  icons: {
    defaultSet: 'fa',
  },
  theme: {
    themes: {
      light: {
        colors: {
          primary: '#3B8DBD',
          secondary: '#222222',
          accent: '#0D0D0D',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FFC107',
        },
      },
    },
  },
});
