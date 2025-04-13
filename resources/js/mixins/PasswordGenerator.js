export default {
  methods: {
    generatePassword (length, chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
      let password = ''
      for (let i = length; i > 0; --i) {
        password += chars[Math.round(Math.random() * (chars.length - 1))]
      }
      return password
    }
  }
}
