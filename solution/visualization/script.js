$(document).ready(function(){

    'use strict';

    $('.diff .mod').hover(
        function(event){
            var $this = $(this);
            toggle_text($this);
        },
        function(event){
            var $this = $(this);
            toggle_text($this);
        }
    );

    var toggle_text = function($this){
        var data_storage = $this.data('storage');
        var temp = $this.text();
        $this.data('storage', temp);
        $this.text(data_storage);
    };

});