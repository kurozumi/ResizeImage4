import unittest
from selenium import webdriver
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait

class E2ETest(unittest.TestCase):

    def setUp(self):
        options = Options()
        options.add_argument('--headless')
        self.driver = webdriver.Chrome(options=options)
        self.wait = WebDriverWait(self.driver, 10)

    def tearDown(self):
        self.driver.close()
        self.driver.quit()

    def test_1(self):
        self.driver.get("http://localhost:8000")
        assert "http://localhost:8000" in self.driver.current_url, self.driver.current_url

    def test_2(self):
        self.driver.get("http://localhost:8000")
        assert "EC-CUBE" in self.driver.title, self.driver.title

if __name__ == "__main__":
    unittest.main()
