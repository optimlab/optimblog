# Install Instructions
1. Install **optimblog.ocmod.zip**.
2. Update modification cache.
3. Go to «User Groups» and assign the appropriate Access Permission:`extension/extension/information` and `extension/information/optimblog`.
4. In the «Extensions -> Extensions» section drop-down list, select «Articles».
5. Activate the OptimBlog module for your store (multi-store module, similar to Theme modules).
6. Edit the OptimBlog module settings and save.
7. Go to «Design» -> «Layouts»:
   - Add Layout «Category Information»
   - Add Route `extension/information/category`
   - Save.
8. Go to «Design» -> «Layouts»:
   - Add Layout «Search Information»
   - Add Route `information/search`
   - Save.

## Adaptation Instructions to your Theme:
If your theme differs from the default theme, then you need to create an additional modification. The modification [optimblog-all-theme-twig.ocmod.zip](https://github.com/optimlab/optimblog/blob/master/dist/optimblog-modification/optimblog-all-theme-twig.ocmod.zip) was created to help. Download, unzip and edit the optimblog-all-theme-twig.ocmod.zip modifier to fit your theme, replacing the path to the theme in the modification code 
from 
`catalog/view/theme/*/` 
to 
`catalog/view/theme/theme_name/`

And then re-zip it under the name **optimblog-theme-name.ocmod.zip**. If it didn’t help or didn’t help completely, then you need to figure it out by fixing it or order an adaptation for your template. You also need to know that the article category template is located at [catalog/view/theme/default/template/information/category.twig](https://github.com/optimlab/optimblog/blob/master/src/optimblog.ocmod/upload/catalog/view/theme/default/template/information/category.twig).
