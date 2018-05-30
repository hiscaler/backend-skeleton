namespace je.web {
    export interface User {
        id: any;
        identity: any;

        setId(id: number): void;

        getId(): number;

        isGuest(): boolean;
    }

    export class User implements je.web.User {
        setId(id: number): void {

        }

        getId() {
            return 0;
        }

        isGuest(): boolean {
            return this.getId() ? true : false;
        }

    }
}