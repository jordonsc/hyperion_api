/**
 * Hyperion engine
 *
 * @constructor
 */
function Hyperion()
{
    var activity = null;
    var router = null;
    var admin = null;

    /**
     * Initialise the engine, should be called on DOM ready
     */
    this.init = function()
    {
        if ($('#activity').length) {
            activity = new Activity(this, '#activity', 'act-progress');
        }
    };

    /**
     * Set a progress bar progress
     *
     * @param {string} name Name of the progress bar
     * @param {int} progress Progress percentage
     */
    this.setProgressBar = function(name, progress)
    {
        $('#' + name + ' .progress-bar').width(progress + '%');
    };

    /**
     * Get the front-end router, lazy loading
     *
     * @returns {Router}
     */
    this.getRouter = function()
    {
        if (router == null) {
            router = new Router();
        }
        return router;
    };

    /**
     * Get the admin object, lazy loading
     *
     * @returns {Admin}
     */
    this.getAdmin = function()
    {
        if (admin == null) {
            admin = new Admin();
        }
        return admin;
    };

    /**
     * Get the activity object, if it exists
     *
     * @returns {Admin}
     */
    this.getActivity = function()
    {
        return activity;
    };

}


var engine = new Hyperion();

$(function()
{
    engine.init();
});
