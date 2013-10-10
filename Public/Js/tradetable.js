$(function ($) {

    // Only support 2 level specs only, which means selectedSpecs length must be 2.
    window.tradetable = function (parent, selectedSpecs, specExtendedAttrs, initSkus) {
        // selectedSpecs: [{fid:'',name:'',values:[]}, {fid:'',name:'',values:[]}]
        // specExtendedAttrs: [{fid:'',name:'',showType:''}]
        this.parent = parent;
        this.selectedSpecs = selectedSpecs;
        this.specExtendedAttrs = specExtendedAttrs;
        this.initSkus = initSkus;
    }

    window.tradetable.prototype = {

        styles: {
            border: '1px solid black'
        },

        createTableHead: function() {
            var html = '<thead>',
                level = 0;

            for (var i in this.selectedSpecs) {
                html += '<th class="t-' + (i + 1) + '">' + this.selectedSpecs[i].name + '</th>';
                level = i + 1;
            }
            for (var i in this.specExtendedAttrs) {
                html += '<th class="t-' + (level + i + 1) + '">';

                html += '<span>' + this.specExtendedAttrs[i].name + '</span>';
              /*html += '<br/>';
                html += '<span>';
                html += '<input type="checkbox"></input>';
                html += '<label>全部相同</label>';
                html += '</span>';*/

                html += '</th>';
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
                html += '<tr>';
                html += '<td class="spec-attr" fid="' + this.selectedSpecs[0].fid + '">' + this.selectedSpecs[0].values[i] + '</td>';

                var sku = querySku(this.initSkus, this.selectedSpecs[0].values[i]);

                for (var o in this.specExtendedAttrs) {
                    var val = '';
                    if (sku != null) {
                        if (this.specExtendedAttrs[o].fname == 'price') {
                            val = sku.price
                        }
                        if (this.specExtendedAttrs[o].fname == 'amountOnSale') {
                            val = sku.quantity;
                        }
                    }
                    html += '<td><input class="txt spec-extend-attr" fname="' + this.specExtendedAttrs[o].fname + '" type="text" value="' + val + '"/></td>';
                }
                html += '</tr>';
            }

            return html;
        },

        handle2Depth: function() {
            var html = '';
            for (var i = 0; i < this.selectedSpecs[0].values.length; i++) {
                for (var j = 0; j < this.selectedSpecs[1].values.length; j++) {
                    html += '<tr>';

                    html += '<td class="spec-attr" fid="' + this.selectedSpecs[0].fid + '">' + this.selectedSpecs[0].values[i] + '</td>';
                    html += '<td class="spec-attr" fid="' + this.selectedSpecs[1].fid + '">' + this.selectedSpecs[1].values[j] + '</td>';

                    var sku = querySku(this.initSkus, this.selectedSpecs[0].values[i], this.selectedSpecs[1].values[j]);

                    for (var o in this.specExtendedAttrs) {
                        var val = '';
                        if (sku != null) {
                            if (this.specExtendedAttrs[o].fname == 'price') {
                                val = sku.price;
                            }
                            if (this.specExtendedAttrs[o].fname == 'amountOnSale') {
                                val = sku.quantity;
                            }
                        }
                        html += '<td>';
                        html += '<input class="txt spec-extend-attr" fname="' + this.specExtendedAttrs[o].fname + '" type="text" value="' + val + '"/>';
                        html += '</td>';
                    }

                    html += '</tr>';
                }
            }

            return html;
        },

        createTable: function() {
            var html = '<table class="tb-speca-quotation" id="tb-speca-quotation-jquery">' + this.createTableHead() + this.createTableBody() + '</table>';
            this.$table = $(this.parent).append(html);
            this.setStyle();
        },

        setStyle: function (styles) {
            if (styles) {
                this.$table.css(styles);
            } else {
                this.$table.css(this.styles);
            }
        },

        removeAll: function() {
            $(this.parent).find('.tb-speca-quotation').remove();
        }

    };

    // public functions
    window.querySku = function (skus) {
        for (var i in skus) {
            var isThis = true;
            for (var j = 1; j < arguments.length; j += 1) {
                if (skus[i].propertiesName.indexOf(arguments[j]) == -1) {
                    isThis = false;
                    break;
                }
            }
            if (isThis) {
                return skus[i];
            }
        }
        return null;
    }

});
