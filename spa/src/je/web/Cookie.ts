namespace je.web {
    
    class Cookie {

        public name: string;
        public value: string;
        public domain: string;
        public expire: string;
        public path = '/';
        public secure = false;
        public httpOnly = true;

        public constructor(name = null, value = null, expire: 365, path: string) {
            this.name = name;
            this.value = value;
            let d = new Date();
            d.setDate(d.getDate() + expire);
            this.expire = d.toUTCString();
            if (path.length) {
                this.path = path;
            }
        }

        public set(): void {
            document.cookie = this.name + '=' + this.value + ';expires=' + this.expire + ';path=' + this.path;
        }

        public get(name: string): any {
            let value = null,
                cookies = document.cookie.split(';');
            for (let c in cookies) {
                let t = c.trim();
                if (t.indexOf(name) === 0) {
                    value = t.substring(name.length, c.length)
                    break;
                }
            }

            return value;
        }

        public remove(name: string): void {
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
        }

        public toString(): string {
            return this.value;
        }

    }
}