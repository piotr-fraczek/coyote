import '../components/subscribe';
import '../plugins/tags';
import Config from '../libs/config';
import Vue from 'vue';
import VueComment from '../components/comment.vue';
import axios from 'axios';
import store from '../store';

new Vue({
    el: '#comments',
    delimiters: ['${', '}'],
    components: {
        'vue-comment': VueComment
    },
    data: {defaultText: ''},
    store,
    created: function () {
        // fill vuex with data passed from controller to view
        store.commit('comments/init', window.data.comments);
    },
    mounted: function () {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = Config.csrfToken();
    },
    methods: {
        submitForm: function (e) {
            axios.post(e.target.action, new FormData(e.target))
                .then(response => {
                    store.commit('comments/add', response.data);
                    this.defaultText = '';
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    computed: {
        comments () {
            return store.state.comments.comments;
        }
    }
});

class Filter {
    constructor(form) {
        this.form = $(form);

        this.onFilterClick();
    }

    onFilterClick() {
        this.form.on('click', '.list-group-item a', e => {
            let checkbox = $(e.currentTarget).prev(':checkbox');

            checkbox.attr('checked', !checkbox.is(':checked'));
            this.onSubmit();

            return false;
        });
    }

    onSubmit() {
        this.form.find('form').submit();
    }
}

$(() => {
    'use strict';

    new Filter('#box-filter');

    $('#editor').on('shown.bs.modal', e => {
        $('#tags').tag({
            promptUrl: Config.get('promptUrl')
        });

        $(e.currentTarget).off('shown.bs.modal');
    });

    /**
     * Save preferences form
     */
    $('#form-preferences').on('submit', (e) => {
        let form = $(e.currentTarget);

        $.post(form.attr('action'), form.serialize(), (url) => {
            $('#editor').modal('hide');
            window.location.href = url;
        })
        .fail((e) => {
            $('.has-error', form).removeClass('has-error');
            $('.help-block', form).text('');

            let errors = e.responseJSON;

            Object.keys(errors).forEach(key => {
                form.find(`[data-column="${key}"]`).addClass('has-error').find('.help-block').text(errors[key][0]);
            });

            $(':submit', form).enable();
        });

        return false;
    });

    $('#btn-editor').click(() => {
        $('#editor').modal('show');

        return false;
    });

    $('a[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

    /**
     * Reload form after click on "x" button
     */
    $('input[type=search]').on('search', function () {
        $(this).closest('form').submit();
    });

    $('a[data-toggle="lightbox"]').click(function() {
        require.ensure([], (require) => {
            require('ekko-lightbox/dist/ekko-lightbox');

            $(this).ekkoLightbox();
        });

        return false;
    });
});
