<?php if($context['isImported']): ?>
    <? if($context['style']): ?>
    <canvas id="glcanvas"
            style="width: <?php echo $context['style']['width']; ?>;<?php if($context['style']['height']):?> height: <?php echo $context['style']['height']; ?>;<?php endif;?><?php echo " " . $context['style']['patchStyle']; ?>"
    />
    <?php else: ?>
        <canvas id="glcanvas"/>
    <?php endif; ?>

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