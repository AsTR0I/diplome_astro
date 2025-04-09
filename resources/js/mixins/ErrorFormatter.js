export default {
  methods: {
    getErrorMessage(error) {
      if (error.response.data.errors) {
        // Laravel validation errors
        let messages = [];
        for (let field in error.response.data.errors) {
          messages.push(error.response.data.errors[field][0]);
          return messages.join(', ');
        }

      } else if (error.response.data.message) {
        // exceptions
        return error.response.data.message;

      } else {
        // other
        return error.toString();
      }
    }
  }
}
