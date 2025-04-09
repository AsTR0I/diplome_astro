export const rules = {
    context: [
      v => !!v || 'Укажите context',
    ],
    exten: [
      v => !!v || 'Укажите exten',

    ],
    priority: [
      v => !!v || 'Укажите priority',
      v => /^[0-9]+$/.test(v) || 'Priority должно быть числом'
    ],
    app: [
      v => !!v || 'Укажите action',

    ],
    appdata: [
      v => !!v || 'Укажите params',
    ],
  };

export default rules;