/**
 * Takes any number of arguments and enforces their type
 * Example:
 * enforceTypes(someString, "string", someNum, "number")
 * 
 * Throws Error if types are mismatched or if arguments are incorrectly structured
 */
function enforceTypes() {
    if (arguments.length % 2 == 1) {
        throw new TypeError("Expected an even number of arguments")
    }

    for (let i = 0; i < arguments.length; i+=2) {
        if (typeof arguments[i+1] !== "string") {
            throw new TypeError("Expected string in argument " + (i + 1) + ", received " + typeof arguments[i+1])
        }

        if (arguments[i+1] == "") {
            throw new TypeError("Argument type cannot be an empty string")
        }

        if (typeof arguments[i] !== arguments[i+1]) {
            throw new TypeError("Expected " + arguments[i+1] + " in argument " + i + ", received " + typeof arguments[i])
        }
    }
}

class Connection {

    static #internal = false
    #username

    /**
     * Private constructor. Use connect() to get instance.
     * 
     * @private
     */
    constructor(username) {
        if (!Connection.#internal) {
            throw new TypeError("Connection constructor is private. Use connect() instead to get an instance")
        }
        this.#username = username
    }

    getUsername() {
        return this.#username
    }

    async getBoards() {
        let data = await Connection.ajax("../cgi_bin/boardController.php", {action: "list_boards"})
        enforceTypes(data, "object", data.success, "boolean", data.data, "object")
        if (!data.success) {
            throw new Error("Could not get list of boards")
        }
        return Object.values(data.data)
    }

    async searchBoards(searchTerms) {
        enforceTypes(searchTerms, "string")
        let data = await Connection.ajax("../cgi_bin/boardController.php", {action: "search_boards", query: searchTerms})
        enforceTypes(data, "object", data.success, "boolean", data.data, "object")
        if (!data.success) {
            throw new Error("Could not get list of searched boards")
        }
        return Object.values(data.data)
    }

    async createBoard(name) {
        enforceTypes(name, "string")
        let data = await Connection.ajax("../cgi_bin/boardController.php", {action: "create_board", board_name: name})
        enforceTypes(data, "object", data.success, "boolean", data.status, "number")
        if (!data.success) {
            throw new Error("Could not get list of searched boards")
        }
        return data.status
    }

    async deleteBoard(id) {
        enforceTypes(id, "number")
        let data = await Connection.ajax("../cgi_bin/boardController.php", {action: "delete_board", board_id: id})
        enforceTypes(data, "object", data.success, "boolean", data.status, "number")
        if (!data.success) {
            throw new Error("Could not get list of searched boards")
        }
        return data.status
    }

    static async connect() {
        let data = await Connection.ajax("../cgi_bin/loginhandler.php", {action: "authenicate"})
        enforceTypes(data, "object", data.success, "boolean")
        if (!data.success) {
            throw new Error("Could not authenicate user, no session variable exists")
        }
        Connection.#internal = true
        let instance = new Connection(data.username)
        Connection.#internal = false
        return instance
    }

    static async ajax(url, data) {
        enforceTypes(url, "string", data, "object")

        try {       
            const response = await fetch(url, {
                method: "POST", // *GET, POST, PUT, DELETE, etc.
                mode: "no-cors", // no-cors, *cors, same-origin
                cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
                credentials: "same-origin", // include, *same-origin, omit
                headers: {
                "Content-Type": "application/json",
                // 'Content-Type': 'application/x-www-form-urlencoded',
                },
                redirect: "follow", // manual, *follow, error
                referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
                body: JSON.stringify(data), // body data type must match "Content-Type" header
            })

            if (!response.ok) {
                throw new Error(`${response.status} ${response.statusText}`)
            }
            let backup = response.clone()
            try {
                return await response.json()
            } catch (error) {
                backup.text().then((data) => {
                    console.log(error, data)
                });
            }
        } catch (error) {
            console.log(error)
        }
    }
}