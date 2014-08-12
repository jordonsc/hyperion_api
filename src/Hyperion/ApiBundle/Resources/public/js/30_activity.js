/**
 * Activity application
 *
 * @constructor
 */
function Activity(engine, el, loader)
{
    var refresh_time = 3000;

    /**
     * Refresh the activity page
     *
     * @param callback
     * @param {int} update Time to call this function again
     * @param {bool} propagate_callback Include the callback on consecutive calls
     */
    this.refresh = function(callback, update, propagate_callback)
    {
        var act = this;
        $.ajax(engine.getRouter().get('dashboard_activities', {}))
            .done(function(data)
            {
                var obj = $.parseJSON(data);

                var active = '';
                $.each(obj.active, function(key, val)
                {
                    active += act.render(val);
                });

                var closed = '';
                $.each(obj.closed, function(key, val)
                {
                    closed += act.render(val);
                });

                $('#activity-alerts').html('');
                $('#active').html(active ? active : '<p>No Active Processes</p>');
                $('#closed').html(closed ? closed : '<p>No Completed Processes</p>');
            })
            .fail(function()
            {
                $('#activity-alerts').html('<div class="alert alert-danger fade in" role="alert"><p>Unable to update activities</p></div>');
            })
            .always(function()
            {
                if (callback) {
                    callback();
                }
                if (update) {
                    setTimeout(function()
                    {
                        if (propagate_callback) {
                            act.refresh(callback, update, propagate_callback);
                        } else {
                            act.refresh(null, update, propagate_callback);
                        }
                    }, update);
                }
            });

    };

    /**
     * Render an action panel
     * @param obj
     * @returns {string}
     */
    this.render = function(obj)
    {
        var out = '<div class="col-lg-4"><div class="panel ' + this.getPanelClass(obj.state) + '"><div class="panel-heading">';
        out += '<h5 class="text-right">' + this.getStateLabel(obj.state) + '</h5>';
        out += '<h3 class="panel-title"><span class="glyphicon ' + this.getActionGlyph(obj.action_type) + '"></span> ' + this.getActionName(obj.action_type);
        out += '</h3></div><div class="panel-body">';

        if (obj.error_message) {
            out += '<div class="alert alert-danger" role="alert">' + obj.error_message + '</div>';
        }

        out += '<table>';
        out += '<tr><th>Action ID</th><td>' + obj.id + '</td></tr>';
        out += '<tr><th>Phase</th><td>' + obj.phase + '</td></tr>';
        out += '<tr><th>Project</th><td>' + obj.project_name + '</td></tr>';
        if (obj.environment_id) {
            out += '<tr><th>Environment</th><td>' + $('<div/>').text(obj.environment_name).html() + '</td></tr>';
        }
        if (obj.distribution_id) {
            out += '<tr><th>Distribution</th><td>' + obj.distribution_name + '</td></tr>';
        }
        out += '</table>';

        out += '</div></div></div>';
        return out;
    };

    /**
     * Get the name of the action state
     *
     * @param {int} action_state
     * @returns {string}
     */
    this.getPanelClass = function(action_state)
    {
        switch (action_state) {
            case 0:
                return 'panel-default';
            case 1:
                return 'panel-primary';
            case 2:
                return 'panel-success';
            case 3:
                return 'panel-danger';
            case 4:
                return 'panel-danger';
            default:
                return 'panel-default';
        }
    };

    /**
     * Get the name of the action state
     *
     * @param {int} action_state
     * @returns {string}
     */
    this.getStateLabel = function(action_state)
    {
        switch (action_state) {
            case 0:
                return '<span class="label label-default">Pending</span>';
            case 1:
                return '<span class="label label-primary">Active</span>';
            case 2:
                return '<span class="label label-success">Completed</span>';
            case 3:
                return '<span class="label label-danger">Failed</span>';
            case 4:
                return '<span class="label label-danger">Timeout</span>';
            default:
                return '<span class="label label-default">Unknown (' + action_state + ')</span>';
        }
    };

    /**
     * Get the name of the action type
     *
     * @param {int} action_type
     * @returns {string}
     */
    this.getActionName = function(action_type)
    {
        switch (action_type) {
            case 0:
                return 'Deploy';
            case 1:
                return 'Scale';
            case 2:
                return 'Tear Down';
            case 3:
                return 'Bake';
            case 4:
                return 'Build';
            default:
                return 'Unknown';
        }
    };

    /**
     * Get the glyicon name of the action type
     *
     * @param {int} action_type
     * @returns {string}
     */
    this.getActionGlyph = function(action_type)
    {
        switch (action_type) {
            case 0:
                return 'glyphicon-globe';
            case 1:
                return 'glyphicon-fullscreen';
            case 2:
                return 'glyphicon-fire';
            case 3:
                return 'glyphicon-hdd';
            case 4:
                return 'glyphicon-cog';
            default:
                return 'glyphicon-question-sign';
        }
    };


    // Init
    if (loader) {
        engine.setProgressBar(loader, 75);
        this.refresh(function()
        {
            $(el).slideDown();
            engine.setProgressBar(loader, 100);
            $('#' + loader).slideUp();
        }, refresh_time, false);
    } else {
        this.refresh(function()
        {
            $(el).slideDown();
        }, refresh_time, false);
    }

}

