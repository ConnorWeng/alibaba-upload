$(function ($) {

    // Only support 2 level specs only, which means selectedSpecs length must be 2.
    window.tradetable = function (parent, selectedSpecs, specExtendedAttrs) {
        // selectedSpecs: [{fid:'',name:'',values:[]}, {fid:'',name:'',values:[]}]
        // specExtendedAttrs: [{fid:'',name:'',showType:''}]
        this.parent = parent;
        this.selectedSpecs = selectedSpecs;
        this.specExtendedAttrs = specExtendedAttrs;
        this.tableHeads = [];
        for (var i in selectedSpecs) {
            this.tableHeads.push(selectedSpecs[i].name);
        }
        for (var i in specExtendedAttrs) {
            this.tableHeads.push(specExtendedAttrs[i].name);
        }
    }

    window.tradetable.prototype = {

        styles: {
            border: '1px solid black'
        },

        createTableHead: function() {
            var html = '<thead>';
            for (var i in this.tableHeads) {
                html += '<th>' + this.tableHeads[i] + '</th>';
            }
            html += '</thead>';

            return html;
        },

        createTableBody: function() {
            var html = '<tbody>',
                loopDepth = this.selectedSpecs.length;

            if (loopDepth === 1) {
                html += this.handle1Depth();
            } else if (loopDepth === 2) {
                html += this.handle2Depth();
            }

            html += '</tbody>';

            return html;
        },

        handle1Depth: function() {
            var html = '';
            for (var i = 0; i < this.selectedSpecs[0].values.length; i++) {
                html += '<tr class="spec-row">';
                html += '<td class="spec-attr" fid="' + this.selectedSpecs[0].fid + '">' + this.selectedSpecs[0].values[i] + '</td>';
                for (var o in this.specExtendedAttrs) {
                    html += '<td><input type="text"/></td>';
                }
                html += '</tr>';
            }

            return html;
        },

        handle2Depth: function() {
            var html = '';
            for (var i = 0; i < this.selectedSpecs[0].values.length; i++) {
                for (var j = 0; j < this.selectedSpecs[1].values.length; j++) {
                    html += '<tr class="spec-row">';

                    html += '<td class="spec-attr" fid="' + this.selectedSpecs[0].fid + '">' + this.selectedSpecs[0].values[i] + '</td>';
                    html += '<td class="spec-attr" fid="' + this.selectedSpecs[1].fid + '">' + this.selectedSpecs[1].values[j] + '</td>';

                    for (var o in this.specExtendedAttrs) {
                        html += '<td><input class="spec-extend-attr" fname="' + this.specExtendedAttrs[o].fname + '" type="text"/></td>';
                    }

                    html += '</tr>';
                }
            }

            return html;
        },

        createTable: function() {
            var html = '<table class="trade-table">' + this.createTableHead() + this.createTableBody() + '</table>';
            this.$table = $(this.parent).append(html);
            this.setStyle();
        },

        setStyle: function (styles) {
            this.$table.find('input').css('width', '140px');

            if (styles) {
                this.$table.css(styles);
            } else {
                this.$table.css(this.styles);
            }
        },

        removeAll: function() {
            $(this.parent).find('.trade-table').remove();
        }

    };

});
