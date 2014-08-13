/**
 * Admin application
 *
 * @constructor
 */
function Admin()
{

    /**
     * Bake an environment
     *
     * @param {int} id
     * @param {string} name
     */
    this.bakeDialogue = function(id, name)
    {
        $('#bake-id').val(id);
        $('#bakeDialogueBody').html('<p>Are you sure you want to start the bake process for environment <b>' + name + '</b>?</p>');
        $('#bakeDialogue').modal();
    };

    /**
     * Call the bake API
     */
    this.bake = function()
    {
        $('#bakeDialogue').modal('hide');

        var bake_id = $('#bake-id').val();

        $.ajax(engine.getRouter().get('api_bake', {'id': bake_id}))
            .done(function()
            {
                window.location = engine.getRouter().get('dashboard_activity');
            })
            .fail(function()
            {
                alert("Error attempting to kick off bakery!");
            });
    }


}
