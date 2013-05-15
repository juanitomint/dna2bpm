//-----this function is for button face
function initButtons(){
    $("button").button();
    //$("button").addClass('dot7');
    $(".btn_search").button({
        icons: {
            primary:'ui-icon-search'
        }

    });
    //----NEW-------------
    $(".btn_new").button({
        icons: {
            primary: "ui-icon-gear"

        }

    });
    //----SAVE-------------
    $(".btn_save").button({
        icons: {
            primary: "ui-icon-disk"

        }

    });
    //----OPEN-------------
    $(".btn_open").button({
        icons: {
            primary: "ui-icon-folder-collapsed"

        }

    });
    //----RELOAD-------------
    $(".btn_reload").button({
        icons: {
            primary: "ui-icon-refresh"

        }

    });
    //----delete-------------
    $(".btn_delete").button({
        icons: {
            primary: "ui-icon-trash"

        }

    });
    //----Home-------------
    $(".btn_home").button({
        icons: {
            primary: "ui-icon-home"

        }

    });
    //----EDIT-------------
    $(".btn_edit").button({
        icons: {
            primary: "ui-icon-pencil"

        }

    });
    //------lock-------------
    $(".btn_lock").button({
        icons: {
            primary: "ui-icon-locked"

        }

    });

    //------Seek First-------------
    $(".btn_seek_first").button({
        icons: {
            primary: "ui-icon-seek-first"

        }

    });
    //------Seek First-------------
    $(".btn_seek_first").button({
        icons: {
            primary: "ui-icon-seek-first"

        }

    });

    //------Seek Prev-------------
    $(".btn_seek_prev").button({
        icons: {
            primary: "ui-icon-seek-prev"

        }

    });
    //------Seek Next-------------
    $(".btn_seek_next").button({
        icons: {
            primary: "ui-icon-seek-next"

        }

    });
    //------Seek End-------------
    $(".btn_seek_end").button({
        icons: {
            primary: "ui-icon-seek-end"

        }

    });



};