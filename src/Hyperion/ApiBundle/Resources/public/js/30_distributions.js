/**
 * Distributions dashboard
 *
 * @constructor
 */
function Distributions(engine, el, loader) {
    var refresh_time = 3000;
    var distro_id = null;

    /**
     * Refresh the distributions page
     *
     * @param callback
     * @param {int} update Time to call this function again
     * @param {bool} propagate_callback Include the callback on consecutive calls
     */
    this.refresh = function(callback, update, propagate_callback) {
        var distro = this;
        $.ajax(engine.getRouter().get('dashboard_distributions_list', {}))
            .done(function(data) {
                var obj = $.parseJSON(data);

                var col = 0;
                var active = '<div class="row">';
                $.each(obj.active, function(key, val) {
                    if (col++ == 3) {
                        col = 1;
                        active += '</div><div class="row">';
                    }
                    active += distro.render(val);
                });
                active += '</div>';

                col = 0;
                var closed = '<div class="row">';
                $.each(obj.closed, function(key, val) {
                    if (col++ == 3) {
                        col = 1;
                        closed += '</div><div class="row">';
                    }
                    closed += distro.render(val);
                });
                closed += '</div>';

                $('#distribution-alerts').html('');
                $('#active').html(obj.active.length ? active : '<p>No Active Distributions</p>');
                $('#closed').html(obj.closed.length ? closed : '<p>No Closed Distributions</p>');
            })
            .fail(function() {
                $('#distribution-alerts').html('<div class="alert alert-danger fade in" role="alert"><p>Unable to update distribution list</p></div>');
            })
            .always(function() {
                if (callback) {
                    callback();
                }
                if (update) {
                    setTimeout(function() {
                        if (propagate_callback) {
                            distro.refresh(callback, update, propagate_callback);
                        } else {
                            distro.refresh(null, update, propagate_callback);
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
    this.render = function(obj) {
        var out = '<div class="col-lg-4"><div class="panel ' + this.getPanelClass(obj.status) + '"><div class="panel-heading">';
        out += '<h5 class="text-right">' + this.getStatusLabel(obj.status) + '</h5>';
        out += '<h3 class="panel-title"><span class="glyphicon ' + this.getStatusGlyph(obj.status) + '"></span> ' + obj.name;
        out += ' <span class="badge">' + obj.version + '</span>';
        out += '</h3></div><div class="panel-body">';

        if (obj.error_message) {
            out += '<div class="alert alert-danger" role="alert">' + obj.error_message + '</div>';
        }

        out += '<table>';
        out += '<tr><th>Build ID</th><td>' + obj.id + '</td></tr>';
        out += '<tr><th>Environment Type</th><td>' + this.getEnvLabel(obj.environment_type) + '</td></tr>';
        out += '<tr><th>Project</th><td>' + $('<div/>').text(obj.project_name).html() + '</td></tr>';
        out += '<tr><th>Environment</th><td>' + $('<div/>').text(obj.environment_name).html() + '</td></tr>';
        if (obj.environment_type == 1) {
            if (obj.instance_id) {
                out += '<tr><th>Instance ID</th><td>' + obj.instance_id + '</td></tr>';
            }
            if (obj.public_ip4) {
                out += '<tr><th>Public IPv4</th><td><a href="http://' + obj.public_ip4 + '/">' + obj.public_ip4 + '</a></td></tr>';
            }
            if (obj.public_ip6) {
                out += '<tr><th>Public IPv6</th><td><a href="http://' + obj.public_dns + '/">' + obj.public_ip6 + '</a></td></tr>';
            }
        } else {
            out += '<tr><th>Instances</th><td>' + obj.instances + '</td></tr>';
        }
        out += '</table><div class="btn-group">';

        if (obj.status == 2 || obj.status > 5) {
            out += '<a href="javascript:engine.getDistributions().rebuildConf(' + obj.id + ')" class="btn btn-xs btn-default">Rebuild</a>';
        }
        if (obj.status == 2 || obj.status == 5 || obj.status == 7) {
            out += '<a href="javascript:engine.getDistributions().tearDownConf(' + obj.id + ')" class="btn btn-xs btn-default">Tear down</a>';
        }

        out += '</div></div></div></div>';
        return out;
    };

    /**
     * Get the name of the action state
     *
     * @param {int} status
     * @returns {string}
     */
    this.getPanelClass = function(status) {
        switch (status) {
            case 0:
                return 'panel-default';
            case 1:
                return 'panel-primary';
            case 2:
                return 'panel-success';
            case 3:
                return 'panel-primary';
            case 4:
                return 'panel-warning';
            case 5:
                return 'panel-info';
            case 6:
                return 'panel-default';
            case 7:
                return 'panel-danger';
            default:
                return 'panel-default';
        }
    };

    /**
     * Get the name of the environment type
     *
     * @param {int} env_type
     * @returns {string}
     */
    this.getEnvLabel = function(env_type) {
        switch (env_type) {
            case 0:
                return '<span class="label label-danger">Bakery</span>';
            case 1:
                return '<span class="label label-warning">Test</span>';
            case 2:
                return '<span class="label label-primary">Production</span>';
            default:
                return '<span class="label label-default">Unknown (' + env_type + ')</span>';
        }
    };

    /**
     * Get the name of the status
     *
     * @param {int} status
     * @returns {string}
     */
    this.getStatusLabel = function(status) {
        switch (status) {
            case 0:
                return '<span class="label label-default">Pending</span>';
            case 1:
                return '<span class="label label-primary">Building</span>';
            case 2:
                return '<span class="label label-success">Online</span>';
            case 3:
                return '<span class="label label-primary">Scaling</span>';
            case 4:
                return '<span class="label label-warning">Terminating</span>';
            case 5:
                return '<span class="label label-info">Frozen</span>';
            case 6:
                return '<span class="label label-default">Terminated</span>';
            case 7:
                return '<span class="label label-danger">Failed</span>';
            default:
                return '<span class="label label-default">Unknown (' + status + ')</span>';
        }
    };

    /**
     * Get the glyphicon name of the action type
     *
     * @param {int} action_type
     * @returns {string}
     */
    this.getStatusGlyph = function(action_type) {
        switch (action_type) {
            case 0:
                return 'glyphicon-time';
            case 1:
                return 'glyphicon-cog';
            case 2:
                return 'glyphicon-flash';
            case 3:
                return 'glyphicon-fullscreen';
            case 4:
                return 'glyphicon-fire';
            case 5:
                return 'glyphicon-flash';
            case 6:
                return 'glyphicon-stop';
            case 7:
                return 'glyphicon-remove-circle';
            default:
                return 'glyphicon-question-sign';
        }
    };

    /**
     * Confirm a rebuild
     *
     * @param {int} id
     */
    this.rebuildConf = function(id) {
        distro_id = id;
        $('#rebuildDialogue').modal();
    };

    /**
     * Confirm a tear-down
     *
     * @param {int} id
     */
    this.tearDownConf = function(id) {
        distro_id = id;
        $('#tearDownDialogue').modal();
    };

    /**
     * Rebuild an environment
     */
    this.rebuild = function() {
        $('#rebuildDialogue').modal('hide');
        $.ajax(engine.getRouter().get('dashboard_rebuild_distribution', {id: distro_id}))
            .fail(function() {
                alert("Error rebuilding distribution");
            });
    };

    /**
     * Tear-down an environment
     */
    this.tearDown = function() {
        $('#tearDownDialogue').modal('hide');
        $.ajax(engine.getRouter().get('dashboard_teardown_distribution', {id: distro_id}))
            .fail(function() {
                alert("Error tearing down distribution");
            });
    };

    // Init
    if (loader) {
        engine.setProgressBar(loader, 75);
        this.refresh(function() {
            $(el).slideDown();
            engine.setProgressBar(loader, 100);
            $('#' + loader).slideUp();
        }, refresh_time, false);
    } else {
        this.refresh(function() {
            $(el).slideDown();
        }, refresh_time, false);
    }

}

