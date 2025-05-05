export const rules = {
    name: [
      v => !!v || 'Укажите name',
    ],
    email: [
      v => !!v || 'Укажите email',
      v => /.+@.+\..+/.test(v) || 'Неверный формат email',
    ],
    password: [
      v => !!v || 'Укажите пароль',
      v => v.length >= 6 || 'Минимум 6 символов',
    ],
    password_confirmation: [
      v => !!v || 'Укажите пароль',
      v => v.length >= 6 || 'Минимум 6 символов',
    ]
  };

export default rules;