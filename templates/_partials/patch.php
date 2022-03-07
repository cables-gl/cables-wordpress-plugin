<?php if($context['isImported']): ?>
    <?php if($context['style']): ?>
    <canvas id="glcanvas"
            style="width: <?php echo $context['style']['width']; ?>;<?php if($context['style']['height']):?> height: <?php echo $context['style']['height']; ?>;<?php endif;?><?php echo " " . $context['style']['patchStyle']; ?>"
    />
    <?php else: ?>
        <canvas id="glcanvas"/>
    <?php endif; ?>

    <script type="text/javascript" src="<?php echo $context['patchDir']; ?>js/patch.js"></script>

    <script>

        function showError(err) {
            alert(err);
        }

        function patchInitialized() {
          console.info("patch initialized")
        }

        function patchFinishedLoading() {
          console.info("patch finshed loading")
        }

        document.addEventListener('CABLES.jsLoaded', function(event) {
            CABLES.patch = new CABLES.Patch(
                {
                    patch: CABLES.exportedPatch,
                    prefixAssetPath: '<?php echo $context['patchDir']; ?>/',
                    glCanvasId: 'glcanvas',
                    glCanvasResizeToWindow: false,
                    onError: showError,
                    onPatchLoaded: patchInitialized,
                    onFinishedLoading: patchFinishedLoading,
                });
        });

    </script>
<?php endif; ?>
