var menuEditor = {
    allCategories: [],
    categorieWithoutParents: [],
    categoriesMain: [],

    getAllCategories: function () {
        $.get('/admin/get_menu',
            function (data) {
                menuEditor.allCategories = data;
                $.each(data, function (index, value) {
                    if (!value.children) {
                        menuEditor.categorieWithoutParents.push(value);
                    }
                });
                menuEditor.createTable();
            }
        );
    },

    createTable: function () {

        $.each(menuEditor.allCategories, function (index, value) {
            var newTr =
                '<tr id="parent-category-' + value.category_id + '" data-parent = "' + value.category_id + '">' +
                '<td class="main-category">' + value.name + '<td>';
            newTr += '<td>';

            if (value.children) {
                $.each(value.children, function (indexChild, valueChild) {
                    newTr +=
                        '<div class="js-child-block" data-child="' + valueChild.category_id + '">' + menuEditor.createSelectBox(menuEditor.categorieWithoutParents, valueChild) +
                        '<i class="fa"></i>' +
                        '</div>';
                });
            }

            if (!value.children) {
                menuEditor.categoriesMain.push(value);
            }

            newTr +=
                '<div class="js-child-block">' + menuEditor.createSelectBox(menuEditor.categorieWithoutParents, null) +
                '<i class="fa"></i>' +
                '</div>' +
                '<div class="lower"></div>' +
                '</tr>';

            $('.js-main-row').after(newTr);
        });
    },

    createSelectBox: function (categories, selected) {
        var newSelect = '<select name="category-child" class="form-control float-left width-80 category-child">';

        if (selected === null) {
            newSelect += '<option value="-" selected> --- </option>'
        }
        else {
            newSelect += '<option value="' + selected.category_id + ' checked">' + selected.name + '</option>';
        }

        newSelect += '</select>';

        if (selected != null) {
            newSelect += menuEditor.addCloseButton();
        }
        return newSelect;
    },

    updateParent: function (parentId, childId, $jsBlock) {
        $.post('/admin/menu/update_parent', {child_id: childId, parent_id: parentId})
            .done(function (data) {
                menuEditor.categorieWithoutParents = data;
                var delI = -1;
                $.each(menuEditor.categoriesMain, function (index, value) {
                    if (value.name == $('#parent-category-' + childId).find('.main-category').text()) {
                        delI = index;
                    }
                });

                menuEditor.categoriesMain.splice(delI, 1);
                $('#parent-category-' + childId).fadeOut(600).remove();

                $jsBlock.find('i').removeClass('fa-spin').removeClass('fa-spinner');
                $jsBlock.data("child", childId);
                $jsBlock.find('select').after(menuEditor.addCloseButton());
                $jsBlock.after(
                    '<div class="js-child-block">' + menuEditor.createSelectBox(menuEditor.categorieWithoutParents, null) +
                    '<i class="fa"></i>' +
                    '</div>'
                );
            });
    },

    deleteChild: function (parentId, childId, childValue, $jsBlock) {
        $.post('/admin/menu/delete_child', {child_id: childId, parent_id: parentId})
            .done(function (data) {
                menuEditor.categorieWithoutParents = data;
                menuEditor.categoriesMain.push({name: childValue, category_id: childId});
                var newTr =
                    '<tr id="parent-category-' + childId + '" data-parent = "' + childId + '">' +
                    '<td class="main-category">' + childValue + '<td>' +
                    '<td>' +
                    '<div class="js-child-block">' + menuEditor.createSelectBox(menuEditor.categorieWithoutParents, null) +
                    '<i class="fa"></i>' +
                    '</div>' +
                    '<td>' +
                    '</tr>';

                $('.js-lower-row').before(newTr);

                $jsBlock.find('i').removeClass('fa-spin').removeClass('fa-spinner');

                $jsBlock.fadeOut(500).remove();

            });
    },

    addCloseButton: function (parent) {
        return '<button class="btn btn-sm btn-danger js-delete-' + (parent ? 'parent' : 'child') + ' float-right" title="Удалить"><span class="fa fa-close"></span></button>';
    },

    addAddButton: function (parent) {
        return '<button class="btn btn-sm btn-success js-add-' + (parent ? 'parent' : 'child') + ' float-left" title="Добавить">Добавить подраздел</button>';
    }
};

    $('body').on('change', '.category-child', function (event) {

        var parentId = $(this).closest('tr').data('parent');
        var childId = $(this).val();

        $(this).closest('.js-child-block').find('i').find('i')
            .addClass('fa-spinner')
            .addClass('fa-spin');
        menuEditor.updateParent(parentId, childId, $(this).closest('.js-child-block'));
    });

    $('body').on('focus', '.category-child', function (event) {

        var options = '';
        var textMain = $(this).closest('tr').find('.main-category').text();

        options += '<option value="'+ $(this).val() +'">' + $(this).find('option:selected').text() + '</option>';
        $.each(menuEditor.categoriesMain, function (index, value) {

            if(textMain != value.name){
                options += '<option value="' + value.category_id + '">' + value.name + '</option>';
            }

        });
        $(this).html(options);
    });


    $('body').on('click', '.js-delete-child', function (event) {

        var parentId = $(this).closest('tr').data('parent');
        var childId = $(this).closest('.js-child-block').data('child');
        var childValue = $(this).closest('.js-child-block').find('select :selected').text();

        $(this).closest('.js-child-block').find('i')
            .addClass('fa-spinner')
            .addClass('fa-spin');

        menuEditor.deleteChild(parentId, childId, childValue, $(this).closest('.js-child-block'));
    });



