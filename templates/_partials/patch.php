<?php if($context['isImported']): ?>
    <canvas id="glcanvas"/>

    <script type="text/javascript" src="<?php echo $context['styleDir']; ?>js/libs.core.min.js"></script>
    <script type="text/javascript" src="<?php echo $context['styleDir']; ?>js/cables.min.js"></script>
    <script type="text/javascript" src="<?php echo $context['styleDir']; ?>js/ops.js"></script>

    <script>

        function showError(err) {
            alert(err);
        }

        window.addEventListener("load", function (event) {
            CABLES.patch = new CABLES.Patch(
                {
                    patchFile: '<?php echo $context['styleDir']; ?>js/patch.json',
                    prefixAssetPath: '<?php echo $context['styleDir']; ?>/',
                    glCanvasId: 'glcanvas',
                    glCanvasResizeToWindow: false,
                    onError: showError
                });
        });

    </script>
<?php endif; ?>