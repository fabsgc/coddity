!function(a) {
    /**
     * @param {string} i
     * @return {?}
     */
    function r(i) {
        if (t[i]) {
            return t[i].exports;
        }
        var m = t[i] = {
            i : i,
            l : false,
            exports : {}
        };
        return a[i].call(m.exports, m, m.exports, r), m.l = true, m.exports;
    }
    var t = {};
    return r.m = a, r.c = t, r.i = function(b) {
        return b;
    }, r.d = function(ctx, e, f) {
        if (!r.o(ctx, e)) {
            Object.defineProperty(ctx, e, {
                configurable : false,
                enumerable : true,
                /** @type {Function} */
                get : f
            });
        }
    }, r.n = function(c) {
        /** @type {function (): ?} */
        var a = c && c.__esModule ? function() {
            return c.default;
        } : function() {
            return c;
        };
        return r.d(a, "a", a), a;
    }, r.o = function(action, options) {
        return Object.prototype.hasOwnProperty.call(action, options);
    }, r.p = "", r(r.s = 2);
}({
    /**
     * @param {?} dataAndEvents
     * @param {?} deepDataAndEvents
     * @return {undefined}
     */
    2 : function(dataAndEvents, deepDataAndEvents) {
        gapi.analytics.ready(function() {
            gapi.analytics.createComponent("ActiveUsers", {
                /**
                 * @return {undefined}
                 */
                initialize : function() {
                    /** @type {number} */
                    this.activeUsers = 0;
                    gapi.analytics.auth.once("signOut", this.handleSignOut_.bind(this));
                },
                /**
                 * @return {undefined}
                 */
                execute : function() {
                    if (this.polling_) {
                        this.stop();
                    }
                    this.render_();
                    if (gapi.analytics.auth.isAuthorized()) {
                        this.pollActiveUsers_();
                    } else {
                        gapi.analytics.auth.once("signIn", this.pollActiveUsers_.bind(this));
                    }
                },
                /**
                 * @return {undefined}
                 */
                stop : function() {
                    clearTimeout(this.timeout_);
                    /** @type {boolean} */
                    this.polling_ = false;
                    this.emit("stop", {
                        activeUsers : this.activeUsers
                    });
                },
                /**
                 * @return {undefined}
                 */
                render_ : function() {
                    var options = this.get();
                    this.container = "string" == typeof options.container ? document.getElementById(options.container) : options.container;
                    this.container.innerHTML = options.template || this.template;
                    this.container.querySelector("b").innerHTML = this.activeUsers;
                },
                /**
                 * @return {undefined}
                 */
                pollActiveUsers_ : function() {
                    var data = this.get();
                    /** @type {number} */
                    var duration = 1E3 * (data.pollingInterval || 5);
                    if (isNaN(duration) || duration < 5E3) {
                        throw new Error("Frequency must be 5 seconds or more.");
                    }
                    /** @type {boolean} */
                    this.polling_ = true;
                    gapi.client.analytics.data.realtime.get({
                        ids : data.ids,
                        metrics : "rt:activeUsers"
                    }).then(function(r) {
                        var results = r.result;
                        /** @type {number} */
                        var max = results.totalResults ? +results.rows[0][0] : 0;
                        var min = this.activeUsers;
                        this.emit("success", {
                            activeUsers : this.activeUsers
                        });
                        if (max != min) {
                            /** @type {number} */
                            this.activeUsers = max;
                            this.onChange_(max - min);
                        }
                        if (1 == this.polling_) {
                            /** @type {number} */
                            this.timeout_ = setTimeout(this.pollActiveUsers_.bind(this), duration);
                        }
                    }.bind(this));
                },
                /**
                 * @param {number} event
                 * @return {undefined}
                 */
                onChange_ : function(event) {
                    var blue = this.container.querySelector("b");
                    if (blue) {
                        blue.innerHTML = this.activeUsers;
                    }
                    this.emit("change", {
                        activeUsers : this.activeUsers,
                        delta : event
                    });
                    if (event > 0) {
                        this.emit("increase", {
                            activeUsers : this.activeUsers,
                            delta : event
                        });
                    } else {
                        this.emit("decrease", {
                            activeUsers : this.activeUsers,
                            delta : event
                        });
                    }
                },
                /**
                 * @return {undefined}
                 */
                handleSignOut_ : function() {
                    this.stop();
                    gapi.analytics.auth.once("signIn", this.handleSignIn_.bind(this));
                },
                /**
                 * @return {undefined}
                 */
                handleSignIn_ : function() {
                    this.pollActiveUsers_();
                    gapi.analytics.auth.once("signOut", this.handleSignOut_.bind(this));
                },
                template : '<div class="ActiveUsers"><b class="ActiveUsers-value"></b> <br/>visiteur(s) en ligne</div>'
            });
        });
    }
});
