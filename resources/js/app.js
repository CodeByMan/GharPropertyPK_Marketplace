import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import VueEasyLightbox from 'vue-easy-lightbox'
import MainLayout from '@/Layouts/MainLayout.vue'
import '../css/app.css'

createInertiaApp({
  title: title => title ? `${title} | GharProperty.pk` : 'GharProperty.pk',
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || MainLayout
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(VueEasyLightbox)
      .mount(el)
  },
  progress: {
    color: '#70b447',
    showSpinner: false,
  },
})
