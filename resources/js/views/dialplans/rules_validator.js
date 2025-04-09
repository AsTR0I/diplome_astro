export const rules = {
    destination: [
      v => !!v || 'Укажите destination',
    ],
    context: [
      v => !!v || 'Укажите context',

    ],
    priority: [
      v => !!v || 'Укажите priority',
      v => /^[0-9]+$/.test(v) || 'Priority должно быть числом'
    ],
    action: [
      v => !!v || 'Укажите action',

    ],
    params: [
      v => !!v || 'Укажите params',
    ],
  };

export default rules;