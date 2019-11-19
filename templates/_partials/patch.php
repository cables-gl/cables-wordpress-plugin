{% if isImported %}
    <canvas id="glcanvas" width="100px" height="100px"></canvas>

    <script type="text/javascript" src="<?php echo $context['styleDir']; ?>js/libs.core.min.js"></script>
    <script type="text/javascript" src="<?php $context['styleDir']; ?>js/cables.min.js"></script>

    <script type="text/javascript" src="<?php $context['styleDir']; ?>js/ops.js"></script>

    <script>

        function showError(err) {
            alert(err);
        }

        document.addEventListener("DOMContentLoaded", function (event) {
            CABLES.patch = new CABLES.Patch(
                {
                    patchFile: '<?php echo $context['styleDir']; ?>js/polyshapes.json',
                    prefixAssetPath: '<?php echo $context['styleDir']; ?>/',
                    glCanvasId: 'glcanvas',
                    glCanvasResizeToWindow: false,
                    onError: showError
                });
        });

    </script>
{% endif %}
