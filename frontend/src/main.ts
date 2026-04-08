import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { createI18n } from 'vue-i18n'
import './assets/app.css'

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        en: {
            nav: { home: "Home", search: "Search", cart: "Cart", saves: "Saves" }
        },
        ar: {
            nav: { home: "الرئيسية", search: "بحث", cart: "السلة", saves: "المحفوظات" }
        }
    }
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(i18n)

app.mount('#app')
