<html>
    <body>
        @configcat('disabled_feature')
            I am hidden
        @endconfigcat

        @unlessconfigcat('disabled_feature')
            I am not hidden
        @endconfigcat

        @configcat('enabled_feature')
            I should be visible
        @else
            I should not be visible
        @endconfigcat

        @configcat('unknown_feature')
            You cannot see me
        @elseconfigcat('enabled_feature')
            You can see me
        @endconfigcat
    </body>
</html>
