(function ($) {
  Drupal.behaviors.wicketOnboardingModule = {
    attach: function (context, settings) {
      $('#wicket-onboarding-content-root', context).once('wicketOnboardingModule', function () {
        var rootEl = this;
        var wicketSettings = settings.wicketOnboarding || {};

        var Wicket_Onboarding_ready = window.Wicket_Onboarding_ready = window.Wicket_Onboarding_ready || [];
        Wicket_Onboarding_ready.push(function () {
          var Wicket = window.Wicket;

          if (Wicket && Wicket.Onboarding) {
            var settings = Object.assign({}, wicketSettings, {
              rootEl: rootEl
            });

            Wicket.Onboarding.initialize(settings);
          } else {
            console.warn('Error wicket is not loaded.');
          }
        });

      });

    }
  }
}(jQuery));
