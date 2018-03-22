import subprocess
import unittest


class TestPluginActivate(unittest.TestCase):

    def setUp(self):
        self.plugin_name = 'wpengine-geoip'
        self.dir = '/var/www/html/wp-content/plugins/{}'.format(self.plugin_name)

    def test_plugin_activate(self):
        run_cmd = 'sudo -u www-data wp plugin activate {}'.format(self.plugin_name)
        exit_code = subprocess.call(run_cmd.split(), cwd=self.dir)
        with self.subTest():
            self.assertEqual(exit_code, 0, 'The plugin ({}) could not be activated'.format(self.plugin_name))
