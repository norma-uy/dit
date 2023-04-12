export class FetchAPI {
    static post = async (url = '', data = {}) => {
        const response = await fetch(url, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            headers: {
                'Content-Type': 'application/json'
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: JSON.stringify(data) // body data type must match "Content-Type" header
        })
        return response.json() // parses JSON response into native JavaScript objects
    }

    static postForm = async (url = '', data = {}) => {
        const response = await fetch(url, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            body: data // body data type must match "Content-Type" header
        })
        return response.json() // parses JSON response into native JavaScript objects
    }

    static get = async (url = '') => {
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            headers: {
                'Content-Type': 'application/json'
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        return response.json() // parses JSON response into native JavaScript objects
    }

    static getHtml = async (url = '') => {
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            headers: {
                'Content-Type': 'text/html; charset=utf-8'
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        return response.json() // parses JSON response into native JavaScript objects
    }
}
