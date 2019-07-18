const express = require('express');

module.exports = function createSearchRouter({ searchService, logger }) {
  const router = express.Router();

  const viewConfig = {
    content: false,
    header: false,
    postscript: false,
  };

  router.get('/', async (req, res, next) => {
    logger.info('GET /search');

    const { establishmentId } = req.app.locals.envVars;

    try {
      let results = [];
      const { query } = req.query;

      if (query) {
        results = await searchService.find({ query, establishmentId });
      }

      return res.render('pages/search', {
        title: 'Search',
        config: viewConfig,
        data: results,
        query,
      });
    } catch (error) {
      return next(error);
    }
  });

  router.get('/suggest', async (req, res, next) => {
    logger.info('GET /search/suggest');

    const { establishmentId } = req.app.locals.envVars;
    const { query } = req.query;

    try {
      const results = await searchService.typeAhead({
        query,
        establishmentId,
      });
      return res.json(results);
    } catch (error) {
      return next(error);
    }
  });

  return router;
};
