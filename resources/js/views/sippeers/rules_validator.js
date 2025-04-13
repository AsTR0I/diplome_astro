export const rules = {
  name : [
      v => !!v || 'Укажите name',
    ],
  type : [
      v => !!v || 'Укажите type',
    ],
  secret : [
      v => !!v || 'Укажите secret',
    ],
  host : [
      v => !!v || 'Укажите host',
    ],
  context : [
      v => !!v || 'Укажите context',
    ],
  nat : [
      v => !!v || 'Укажите nat',
    ],
  directmedia : [
      v => !!v || 'Укажите directmedia',
    ],
  allow : [
      v => !!v || 'Укажите allow',
    ],
    ip_addr: [
      v => !!v || 'Укажите IP',
      v => {
        const ipv4Regex = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;
        return ipv4Regex.test(v) || 'Некорректный IPv4 адрес';
      }
    ]
  };

export default rules;