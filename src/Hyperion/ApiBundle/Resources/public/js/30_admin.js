/**
 * Admin application
 *
 * @constructor
 */
function Admin()
{
    var bake_id = null;
    var build_id = null;
    var release_id = null;

    /**
     * Bake an environment
     *
     * @param {int} id
     * @param {string} name
     */
    this.bakeDialogue = function(id, name)
    {
        bake_id = id;
        $('#bakeDialogueBody').html('<p>Are you sure you want to start the bake process for environment <b>' + name + '</b>?</p>');
        $('#bakeDialogue').modal();
    };

    /**
     * Call the bake API
     */
    this.bake = function()
    {
        $('#bakeDialogue').modal('hide');

        $.ajax(engine.getRouter().get('api_bake', {'id': bake_id}))
            .done(function()
            {
                window.location = engine.getRouter().get('dashboard_activity');
            })
            .fail(function()
            {
                alert("Error starting bakery");
            });
    };

    /**
     * Build an environment
     *
     * @param {int} project_id
     * @param {int} environment_id
     */
    this.buildDialogue = function(project_id, environment_id)
    {
        build_id = environment_id;
        $('#buildDialogue').modal();
        $('#repo-branches').html('<i>Loading..</i>');

        $.ajax(engine.getRouter().get('admin_project_branches', {'id': project_id}))
            .done(function(data)
            {
                var html = '';
                var obj = $.parseJSON(data);
                $.each(obj, function(key, val)
                {
                    html += '<div class="form-group"><div class="input-group"><span class="input-group-addon">' + val['name'] + '</span>';
                    html += '<input name="repo-' + key + '" type="text" class="form-control" placeholder="' + (val['tag'] == null ? '' : val['tag']) + '"></div></div>';
                });
                $('#repo-branches').html(html);
            })
            .fail(function()
            {
                alert("Error retrieving repository list");
            });
    };

    /**
     * Call the build API
     */
    this.build = function()
    {
        var form_data = $('#build-form').serialize();
        $('#buildDialogue').modal('hide');

        $.post(engine.getRouter().get('admin_environment_build', {'id': build_id}), form_data)
            .done(function(data)
            {
                var obj = $.parseJSON(data);
                if (obj.result == 'error') {
                    alert(obj.message);
                } else if (obj.result == 'success') {
                    window.location = engine.getRouter().get('dashboard_activity');
                } else {
                    alert('Unknown response from server');
                }
            })
            .fail(function()
            {
                alert("Error starting build process");
            });
    };

    /**
     * Release an environment
     *
     * @param {int} environment_id
     * @param {string} name
     */
    this.releaseDialogue = function(environment_id, name)
    {
        release_id = environment_id;
        $('#releaseDialogueBody').html('<p>Are you sure you want to RELEASE environment <b>' + name + '</b>?</p>');
        $('#releaseDialogue').modal();
    };

    /**
     * Call the release API
     */
    this.release = function()
    {
        $('#releaseDialogue').modal('hide');

        $.ajax(engine.getRouter().get('api_release', {'id': release_id}))
            .done(function()
            {
                window.location = engine.getRouter().get('dashboard_activity');
            })
            .fail(function()
            {
                alert("Error starting release");
            });
    };

    /**
     * Admin environment form change event
     */
    this.onEnvChange = function(val)
    {
        if (val == 2) {
            $('#environment_group_prod').show();
            $('#environment_group_prod_label').show();
        } else {
            $('#environment_group_prod').hide();
            $('#environment_group_prod_label').hide();
        }
    };

    // Init
    var self = this;
    $('#environment_environment_type').change(function() {
        self.onEnvChange($(this).val());
    }).each(function () {
        self.onEnvChange($(this).val());
    });

}
