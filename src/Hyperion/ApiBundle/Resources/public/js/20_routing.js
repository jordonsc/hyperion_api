/**
 * Routing exposed to the front-end
 */
var Router = function () {
    var routes = {
        // API
        api_bake: '/api/v1/bake/{id}',
        api_build: '/api/v1/build/{id}',

        // Admin
        admin_home: '/admin/',
        admin_project_branches: '/admin/project/{id}/branches',
        admin_environment_build: '/admin/environment/build/{id}',

        // Dashboard
        dashboard_activity: '/dashboard/activity',
        dashboard_activities: '/dashboard/activities.json',
        dashboard_activity_output: '/dashboard/activity/{id}/output.{format}',
        dashboard_distributions: '/dashboard/distributions',
        dashboard_distributions_list: '/dashboard/distribution-list.json',
        dashboard_rebuild_distribution: '/dashboard/distribution/rebuild/{id}',
        dashboard_teardown_distribution: '/dashboard/distribution/teardown/{id}'
    };

    /**
     * Resolve a route
     *
     * @param {string} route
     * @param {Array} params
     * @returns {string}
     */
    this.get = function (route, params) {
        if (!routes[route]) {
            return null;
        }

        var path = routes[route];

        if (params !== undefined) {
            $.each(params, function (key, val) {
                var regex = new RegExp('{' + key + '}', 'g');
                path = path.replace(regex, val);
            });
        }

        return path;
    };

};

