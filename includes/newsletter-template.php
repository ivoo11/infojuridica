<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?= ij_e($edition['subject']) ?></title></head>
<body style="margin:0;background:#f2f2f2;color:#171717;font-family:Arial,Helvetica,sans-serif;">
<?php if (empty($newsletterIsEmail)): ?><div style="padding:14px;text-align:center;font-size:13px;background:#fff;border-bottom:1px solid #ddd;">Vista previa · No se envió ningún correo · <a style="color:#711b2d;" href="/admin/newsletter-editar.php?id=<?= (int)$edition['id'] ?>">Volver a editar</a></div><?php endif; ?>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#fff;"><tr><td style="padding:28px 24px 20px;border-bottom:2px solid #711b2d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td align="center" style="text-align:center;padding-bottom:24px;"><a href="https://infojuridica.com.ar/" style="text-decoration:none;color:#171717;"><img src="https://infojuridica.com.ar/assets/img/logo-infojuridica.png" width="240" alt="INFO JURÍDICA" style="display:block;width:240px;max-width:100%;height:auto;margin:0 auto;border:0;"></a></td></tr>
<tr><td><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="left" style="color:#777;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;">Newsletter</td><td align="right" style="text-align:right;color:#777;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;"><?= ij_date($edition['send_date'] ?? ij_now()) ?></td></tr></table></td></tr>
</table></td></tr>
<?php if ($edition['introduction']!==''): ?><tr><td style="padding:24px 24px 0;font-size:16px;line-height:1.6;"><?= nl2br(ij_e($edition['introduction'])) ?></td></tr><?php endif; ?>
<?php if (count($posts)!==count($edition['ids'])): ?><tr><td style="padding:24px;color:#711b2d;">Una noticia seleccionada ya no está disponible. Volvé al editor para reemplazarla.</td></tr><?php endif; ?>
<?php foreach ($posts as $index=>$post): $url=ij_newsletter_url($post['url']); ?><tr><td style="padding:24px;border-bottom:1px solid #e3e0da;">
<?php if ($index===0 && $post['image']): ?><a href="<?= ij_e($url) ?>"><img src="<?= ij_e(ij_newsletter_url($post['image'])) ?>" alt="<?= ij_e($post['image_alt']) ?>" width="552" style="display:block;width:100%;height:auto;border:0;margin-bottom:20px;"></a><?php endif; ?>
<p style="color:#711b2d;font-size:11px;letter-spacing:1px;margin:0 0 10px;"><?= ij_e($post['categoria']) ?></p>
<h2 style="font-family:Georgia,serif;font-size:<?= $index===0 ? '28' : '22' ?>px;line-height:1.25;margin:0 0 12px;font-weight:normal;"><a href="<?= ij_e($url) ?>" style="color:#171717;text-decoration:none;"><?= ij_e($post['title']) ?></a></h2>
<p style="font-size:15px;line-height:1.6;color:#555;margin:0 0 14px;"><?= ij_e($post['summary']) ?></p>
<a href="<?= ij_e($url) ?>" style="color:#711b2d;font-size:14px;">Leer noticia</a>
</td></tr><?php endforeach; ?>
<?php if ($events): ?><tr><td style="padding:24px;"><h2 style="font-family:Georgia,serif;font-size:24px;font-weight:normal;margin:0 0 18px;">Próximas actividades</h2><?php foreach ($events as $event): ?><p style="margin:0 0 18px;font-size:14px;line-height:1.5;"><a style="color:#711b2d;" href="<?= ij_e(ij_newsletter_url('/agenda/actividad.php?id='.(int)$event['id'])) ?>"><?= ij_e($event['title']) ?></a><br><span style="color:#666;"><?= ij_e($event['newsletter_dates'] ?? ij_event_card_schedule(ij_event_days($event))['dates']) ?></span></p><?php endforeach; ?></td></tr><?php endif; ?>
<tr><td align="center" style="padding:30px 24px 22px;background:#f4eee5;border-top:2px solid #711b2d;text-align:center;">
<p style="font-family:Georgia,serif;font-size:25px;line-height:1.2;color:#711b2d;margin:0 0 10px;">INFO JURÍDICA</p>
<p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#635c59;margin:0 0 20px;">Información para la comunidad jurídica argentina.</p>
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;"><tr><td align="center" bgcolor="#711b2d" style="background:#711b2d;"><a href="https://www.instagram.com/infojuridicaarg/" style="display:inline-block;padding:13px 16px;border:1px solid #711b2d;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;line-height:1.5;color:#fff;text-decoration:none;">Instagram &nbsp;·&nbsp; @infojuridicaarg</a></td></tr></table>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:24px;border-top:1px solid #d8cec5;"><tr>
<td align="left" valign="top" style="padding-top:16px;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6;"><a href="https://infojuridica.com.ar/" style="color:#711b2d;text-decoration:underline;">Visitar el portal</a></td>
<td align="right" valign="top" style="padding-top:16px;padding-left:16px;text-align:right;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6;color:#635c59;"><?php if (!empty($newsletterIsEmail) && !empty($unsubscribeUrl)): ?><a href="<?= ij_e($unsubscribeUrl) ?>" style="color:#635c59;text-decoration:underline;">Darme de baja</a><?php else: ?><?= !empty($newsletterIsEmail) ? 'Correo de prueba. El envío real incluirá tu enlace de baja.' : 'Darme de baja · Enlace personal en el envío.' ?><?php endif; ?></td>
</tr></table>
</td></tr>
</table></td></tr></table><?php if (empty($newsletterIsEmail)): ?><script>if(window.opener)window.opener.postMessage({type:"newsletter-saved",id:<?= (int)$edition['id'] ?>},location.origin);</script><?php endif; ?></body></html>
