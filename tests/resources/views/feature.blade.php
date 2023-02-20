<html>
    <body>
        @configcat('enabled_feature')
          I should be visible
        @else
          I should not be visible
        @endconfigcat

        @configcat('disabled_feature')
          I am hidden
        @endconfigcat
    </body>
</html>
