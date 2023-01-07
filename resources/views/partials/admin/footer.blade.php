<footer class="dash-footer">
  <div class="footer-wrapper">
    <div class="py-1">
      <span class="text-muted"
        >{{__('Copyright')}} &copy; {{ (Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'LeadGo') }} {{date('Y')}}</span
      >
    </div>
  </div>
</footer>
