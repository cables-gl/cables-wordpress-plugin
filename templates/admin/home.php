<h1>Cables Dashboard</h1>

<?php if(!$options['api_key']): ?>
    In order to import your patches from cables, you need to be a registered cables member and enter your cables API-key in the settings area
<?php else: ?>
    <?php if(empty($options['importedPatches'])): ?>
        No imported Patches yet
    <?php else: ?>
        Recently imported:
        <p>
        <?php foreach($options['importedPatches'] as $patch):  ?>
            <?php echo $patch.name ?>
        <?php endforeach; ?>
        </p>
    <?php endif; ?>
<?php endif; ?>
