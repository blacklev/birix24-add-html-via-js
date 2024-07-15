const addTaskToIt = BX.namespace('addTaskToIt');

addTaskToIt.htmlBlock = '';

addTaskToIt.setHtmlBlock = function (e) {
    addTaskToIt.htmlBlock = e;
}

addTaskToIt.addHtmlBlock = function () {
    const headerSearch = document.querySelector('.header-search');

    if (!headerSearch) {
        return;
    }

    headerSearch.insertAdjacentHTML('afterend', addTaskToIt.htmlBlock);
}