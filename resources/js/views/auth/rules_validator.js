export const rules = {
    name: [
      v => !!v || 'Укажите имя',
    ],
    email: [
      v => !!v || 'Укажите почту',
      v => /.+@.+\..+/.test(v) || 'Почта не валидна',
    ],
    password: [
      v => !!v || 'Укажите пароль',
    ],
  };

export default rules;