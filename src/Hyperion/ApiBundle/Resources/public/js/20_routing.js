/**
 * Routing exposed to the front-end
 */
var Router = function()
{
    var routes = {
        // API
        api_bake: '/api/v1/bake/{id}',

        // Admin
        admin_home: '/admin/',

        // Dashboard
        dashboard_activity: '/dashboard/activity',
        dashboard_activities: '/dashboard/activities.json'

    };

    /**
     * Resolve a route
     *
     * @param {string} route
     * @param {Array} params
     * @returns {string}
     */
    this.get = function(route, params)
    {
        if (!routes[route]) {
            return null;
        }

        var path = routes[route];

        if (params !== undefined) {
            $.each(params, function(key, val)
            {
                var regex = new RegExp('{' + key + '}', 'g');
                path = path.replace(regex, val);
            });
        }

        return path;
    };

};

