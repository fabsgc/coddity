// Init vars
addingIndexes = new Array();

$(document).ready(function() {

    // Handle uniqifying
    $('input.uniqify').on('click', function(event) {
        console.log('uniqify : ' + $(this).data('uniqify-id'));
        $("input[data-uniqify-id='" + $(this).data('uniqify-id') + "']").prop('checked', false);
        $(this).prop('checked', true);
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // Embed forms fonctions

    // Initialisation
    /**
     * This will always add empty object if the embedded form type has class "with_empty"
     * This will add empty object only if no object exists if the embedded form type has class "with_default"
     * Number of added object is defined in data-number-to-add attribute of the embedded form type
     */
    $('div').find('*[data-prototype]').each(function(_id) {
        // init vars
        existingObjects = false;
        // adding remove button for existing embedded forms items
        $(this).children().each(function(_id) {
            $(this).append(createRemoveButton());
            existingObjects = true;
        });
        // creating default empty object for each embedded forms
        if ($(this).hasClass('with_empty') || $(this).hasClass('with_default') && !existingObjects) {
            addObject($(this).attr('id'));
        }
        // creating add button
        $(this).parent().append(createAddButton($(this).attr('id')));
    });


    /**
     * Function creating embedded forms items
     * @param {type} _id
     * @returns {undefined}
     */
    function addObject(_id) {
        console.log("Embedded form : add object " + _id);
        // handle items indexes in an array
        if(!addingIndexes[_id]) {
            console.log('creating indexes for ' + _id);
            addingIndexes[_id] = $('#' + _id).children().length;
        } else {
            console.log('increment indexes for ' + _id);
            addingIndexes[_id]++;
        }

        // add objects
        numberToAdd = $('#' + _id).data('number-to-add');
        for (i = 0; i < numberToAdd; i++) {
            $('#' + _id).append(
                $($('#' + _id).attr('data-prototype').replace(/__name__label__/g, '').replace(/__name__/g, addingIndexes[_id])).append(createRemoveButton)
            );
        }
    }

    /**
     * Function creating add button. You can create your own btnAdd js var in your template.
     * @param {type} _toAddId
     * @returns {String}
     */
    function createAddButton(_toAddId) {
        thisBtnAdd = '<a style="cursor: pointer" class="add_object" data-objectid="' + _toAddId + '"><button class="btn btn-default btn-sm"><i class="fa fa-plus"></i></button></a>';
        if (typeof btnAdd !== 'undefined') {
            btnAdd = $.parseHTML(btnAdd);
            $(btnAdd).attr('data-objectid', _toAddId);
            thisBtnAdd = btnAdd;
        }
        return thisBtnAdd;
    }

    /**
     * function creating remove button. You can create your own btnRemove js var in your template.
     * @returns {String}
     */
    function createRemoveButton() {
        thisBtnRemove = '<a style="cursor: pointer;" class="remove_object"><button class="btn btn-default btn-sm" style="margin-top:8px;"><i class="fa fa-remove"></i></button></a>';
        if (typeof btnRemove !== 'undefined') {
            thisBtnRemove = btnRemove;
        }
        return thisBtnRemove;
    }

    // Handle add buttons' click event
    $('.add_object').click(function() {
        addObject($(this).attr('data-objectid'));
    });

    // Handle remove buttons' click event
    $('form').on('click', '.remove_object', function() {
        $(this).parent().remove();
    });
    ///////////////////////////////////////////////////////////////////////////////////////////////

});