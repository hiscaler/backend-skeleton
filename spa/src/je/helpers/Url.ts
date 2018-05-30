namespace je.helpers {

    class Url {

        toRoute(schema: string, params: {}) {
            let route = [schema];
            for (let k in params) {
                route.push(k + '=' + params[k])
            }

            return route.join('&');
        }

        redirect(url: string): void {
            document.location.href = url;
        }

    }

}
