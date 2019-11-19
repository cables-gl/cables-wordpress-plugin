<?php if($context['isImported']): ?>
    <canvas id="glcanvas"/>

    <script type="text/javascript" src="<?php echo $context['patchDir']; ?>js/libs.core.min.js"></script>
    <script type="text/javascript" src="<?php echo $context['patchDir']; ?>js/cables.min.js"></script>
    <script type="text/javascript" src="<?php echo $context['patchDir']; ?>js/ops.js"></script>

    <script>

        function showError(err) {
            alert(err);
        }

        window.addEventListener("load", function (event) {
            CABLES.patch = new CABLES.Patch(
                {
                    patchFile: '<?php echo $context['patchDir']; ?>js/patch.json',
                    prefixAssetPath: '<?php echo $context['patchDir']; ?>/',
                    glCanvasId: 'glcanvas',
                    glCanvasResizeToWindow: false,
                    onError: showError
                });
        });

    </script>
<?php endif; ?>