import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        let el = document.getElementById('button-to-loader');
        let el2 = document.getElementById('insert-loader');
        if(el) {
            el.addEventListener("click", (e) => {
                let el3 = document.getElementById('load-data');
                el3.innerHTML = '';
                let url = document.getElementById('get_image_url').value;
                e.preventDefault()
                el2.classList.add('loader')
                this.getData(el2, url).then()
            }, false)
        }
    }

    async getData(el2, url) {
        if (this.isValidURL(url)){
            const response = await fetch('/url', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ url: url })
            });
            this.element.innerHTML = await response.text();
        } else {
            const response = await fetch('/error', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ url: url })
            });
            this.element.innerHTML = await response.text();
        }
        el2.classList.remove('loader')
    }

    isValidURL(url) {
        try {
            new URL(url);
            return true; // URL корректен
        } catch (e) {
            return false; // URL некорректен
        }
    }
}
