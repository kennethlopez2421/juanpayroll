function Timer(t, fn) {
   this.fn = fn;
   this.time = Date.now() + t;
   this.updateTimer();
}

Timer.prototype.addTime = function(t) {
    this.time += t;
    this.updateTimer();
}

Timer.prototype.stop = function() {
    if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
    }
}

Timer.prototype.updateTimer = function() {
    var self = this;
    this.stop();
    var delta = this.time - Date.now();
    if (delta > 0) {
        this.timer = setTimeout(function() {
            self.timer = null;
            self.fn();
        }, delta);
    }
}
