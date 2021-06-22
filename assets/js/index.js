import 'babel-polyfill';

components['index'] =  {
    delimiters: ['${', '}'],
    template: '#index',
    data: function () {
        return {
            controller: controller_name
        };
    },
}