<?php
    if ($this->system->debug()):
?>
<style type="text/css">
    .debug-bar {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #000000;
        color: #ffffff;
        font-size: 0.9em;
        padding: 10px;
        margin: 30px 0 0 0;
    }
    footer {
        margin-bottom: 80px;
    }
    .collapse-results {
        background-color: #ffffff;
        max-height: 300px;
        overflow: scroll;
        width: 100%;
        padding: 10px;
        color: black;
        margin: 0 0 8px 0;
    }
    .debug-bar .btn.collapsed {
        color: #afafaf;
    }

</style>
<div class="debug-bar">
    <div class="container">
        <div class="collapse" id="debugTemplate">
            <div class="collapse-results">
                <h3>Template</h3>

                <ul class="list-group">
                    <?php foreach ($this->getTemplateData() as $field => $value) {
                            if ($field == 'system') continue;
                            if (!is_scalar($value)) continue;
                        ?>
                        <li class="list-group-item"><strong><?php echo $field; ?></strong> <?php echo htmlentities($value); ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="collapse" id="debugRouter">
            <div class="collapse-results">
                <h3>Route</h3>
                <?php
                /**
                 * @var \Slab\Router\Route $route
                 */
                $route = $this->system->router()->getSelectedRoute();
                if (!empty($route))
                {
                    ?>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Name</strong> <?php echo $route->getName(); ?></li>
                        <li class="list-group-item"><strong>Path</strong> <?php echo $route->getPath() . $route->getPatternString(); ?></li>
                        <li class="list-group-item"><strong>Class</strong> <?php echo $route->getClass(); ?></li>
                        <?php if ($route->getParameters()) { ?>
                            <li class="list-group-item"><strong>Parameters</strong></li>
                            <ul class="list-group">
                            <?php foreach ($route->getParameters() as $parameter => $value): ?>
                                <li class="list-group-item"><strong><?php echo $parameter; ?></strong> <?php echo htmlentities($value); ?></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php } ?>
                    </ul>
                    <?php
                } else {
                    ?>
                    <p>Route information is not available.</p>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="collapse" id="debugInput">
            <div class="collapse-results">
                <h3>Input</h3>
                <?php
                    $get = $this->system->input()->get();
                    $post = $this->system->input()->post();
                    $cookie = $this->system->input()->cookie();
                ?>

                <?php
                    if (!empty($get)) {
                ?>
                    <h4>GET</h4>
                    <ul class="list-group">
                        <?php foreach ($get as $field => $value) {
                            ?>
                            <li class="list-group-item"><strong><?php echo $field; ?></strong> <?php echo htmlentities($value); ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php
                if (!empty($get)) {
                    ?>
                    <h4>POST</h4>
                    <ul class="list-group">
                        <?php foreach ($post as $field => $value) {
                            ?>
                            <li class="list-group-item"><strong><?php echo $field; ?></strong> <?php echo htmlentities($value); ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php
                if (!empty($get)) {
                    ?>
                    <h4>Cookies</h4>
                    <ul class="list-group">
                        <?php foreach ($cookie as $field => $value) {
                            ?>
                            <li class="list-group-item"><strong><?php echo $field; ?></strong> <?php echo htmlentities($value); ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <div class="collapse" id="debugSession">
            <div class="collapse-results">
                <h4>Session</h4>
                <?php
                if ($this->system->session()) {
                    $session = $this->system->session()->get();

                    if (!empty($session)) {
                        ?>
                        <ul class="list-group">
                            <?php foreach ($session as $field => $value) {
                                ?>
                                <li class="list-group-item">
                                    <strong><?php echo $field; ?></strong> <?php echo htmlentities($value); ?></li>
                            <?php } ?>
                        </ul>
                    <?php }
                }?>
            </div>
        </div>
        <div class="collapse" id="debugBenchmarks">
            <div class="collapse-results">
                <h4>Benchmarks</h4>
                <div class="row">
                    <div class="col-md-2">
                        <strong>name</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>elapsed time</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>mem usage</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>mem usage real</strong>
                    </div>
                </div>
                <?php
                    /**
                     * @var \Slab\Components\Debug\BenchmarkInterface[] $benchmarks
                     */
                    $benchmarks = $this->system->debug()->getBenchmarks();
                    ?>
                <?php foreach ($benchmarks as $name => $benchmark) { ?>
                    <div class="panel">
                        <div class="row">
                            <div class="col-md-2">
                                <?php echo $name; ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo number_format($benchmark->getElapsedTime() * 1000, 4); ?>ms
                            </div>
                            <div class="col-md-2">
                                <?php echo number_format($benchmark->getMemoryUsage() / 1024); ?>KB
                            </div>
                            <div class="col-md-2">
                                <?php echo number_format($benchmark->getMemoryUsageReal() / 1024); ?>KB
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="collapse" id="debugMessages">
            <div class="collapse-results">
                <h4>Messages</h4>
                <?php
                $messages = $this->system->debug()->getMessages();
                foreach ($messages as $section => $sectionMessages) {
                    ?>
                    <h5><?php echo $section; ?></h5>
                    <ul class="list-group">
                    <?php
                    foreach ($sectionMessages as $message)
                    {
                        /**
                         * @var \Slab\Components\Debug\MessageInterface $message
                         */
                        ?><li class="list-group-item"><?php $message->formatMessage(); ?></li><?php
                    }
                    ?>
                    </ul>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <strong>SlabPHP Debug</strong>
            </div>
            <div class="col-md-10">
                <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugTemplate" aria-expanded="false" aria-controls="collapseExample">
                    Template
                </button>
                <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugRouter" aria-expanded="false" aria-controls="collapseExample">
                    Route
                </button>
                <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugInput" aria-expanded="false" aria-controls="collapseExample">
                    Input
                </button>
                <?php if ($this->system->session()) { ?>
                    <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugSession" aria-expanded="false" aria-controls="collapseExample">
                        Session
                    </button>
                <?php } ?>
                <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugBenchmarks" aria-expanded="false" aria-controls="collapseExample">
                    Benchmarks
                </button>
                <button class="btn btn-default btn-sm collapsed" type="button" data-toggle="collapse" data-target="#debugMessages" aria-expanded="false" aria-controls="collapseExample">
                    Messages
                </button>
            </div>
        </div>
    </div>
</div>
<?php
    endif;
?>