<?php if(is_array($context['patches']) && count($context['patches']) > 0): ?>
    <script type="text/javascript">
        function showError(err) {
            alert(err);
        }
    </script>
    <script type="text/javascript">
        window.addEventListener("load", function (event) {
            CABLES.EMBED.replaceWithPatch = function (_element, options) {
                var el = _element;
                var id = CABLES.generateUUID();
                if (typeof _element === "string") {
                    id = _element;
                    el = document.getElementById(id);
                    if (!el) {
                        console.error(id + ' Patch Container Element not found!');
                        return;
                    }
                }
                var canvEl = document.createElement("canvas");
                canvEl.id = "glcanvas_" + id;
                canvEl.width = el.clientWidth;
                canvEl.height = el.clientHeight;

                window.addEventListener('resize', function () {
                    this.setAttribute("width", el.clientWidth);
                    this.height = el.clientHeight;
                }.bind(canvEl));

                el.innerHTML = '';
                el.appendChild(canvEl);

                options = options || {};
                options.glCanvasId = canvEl.id;

                if (!options.onError) {
                    options.onError = function (err) {
                        console.log(err);
                    };
                }

                CABLES.patch = new CABLES.Patch(options);
                return canvEl;

            };
        });
    </script>
<?php endif; ?>
<?php if($context['patches']): foreach($context['patches'] as $config): ?>

    <script type="text/javascript" src="<?php echo $config['patchDir']; ?>js/libs.core.min.js"></script>
    <script type="text/javascript" src="<?php echo $config['patchDir']; ?>js/cables.min.js"></script>
    <script type="text/javascript" src="<?php echo $config['patchDir']; ?>js/ops.js"></script>

    <?php if(!empty($config['cssSelector'])): ?>
        <script type="text/javascript">
            window.addEventListener("load", function (event) {
                var nodeList = document.querySelectorAll('<?php echo $config['cssSelector']; ?>');
                if(typeof CABLES.EMBED.replaceWithPatch === 'function') {
                  for (var i = 0; i < nodeList.length; i++) {
                    CABLES.EMBED.replaceWithPatch(nodeList[i], {
                      patchFile: '<?php echo $config['patchDir']; ?>js/patch.json',
                      prefixAssetPath: '<?php echo $config['patchDir']; ?>/',
                      onError: showError,
                      glCanvasResizeToWindow: true
                    });
                  }
                }
            });
        </script>
    <?php endif; ?>
    <?php if($config['background']): ?>
        <script type="text/javascript">
            window.addEventListener("load", function (event) {
                var canvEl = document.createElement("canvas");
                canvEl.id = 'cables_background';
                canvEl.style = 'visibility: visible; width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: -5711;';
                var body = document.getElementsByTagName("body")[0];
                body.insertBefore(canvEl, body.firstChild);
                patchBackground = new CABLES.Patch(
                    {
                        patchFile: '<?php echo $config['patchDir']; ?>js/patch.json',
                        prefixAssetPath: '<?php echo $config['patchDir']; ?>/',
                        glCanvasId: 'cables_background',
                        glCanvasResizeToWindow: true,
                        onError: showError
                    });
                patchBackground.resume();
            });
        </script>
    <?php endif; ?>

<?php endforeach; endif; ?>
