<?php if (false) { ?>
<style type="text/css" media="none">
	html body footer div.container {
		padding: 25px;
		width: calc(100% - 50px); height: calc(230px - 50px);
	}
	html body footer div.container div.wrapper {
		padding: 10px;
		position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);
		width: 420px;
		border-radius: 25px; text-align: center;
		transition: var(--time-tst-xfast);
	}
	html body footer div.container div.wrapper:hover { background-color: var(--fade-black-8); }
	@media only screen and (max-width: 768px) { html body footer div.container div.wrapper { transform: translate(-50%, -50%) scale(0.75); } }
	html body footer div.container div.wrapper div.row { margin: 2.5px 0px; padding: 0px 5px; }
	html body footer div.container div.wrapper div.row:nth-child(1) {
		font-size: 18.75px; letter-spacing: 1.5px; text-transform: uppercase; font-weight: 500;
		color: var(--sys-footer-head-color);
	}
	html body footer div.container div.wrapper div.row:nth-child(2) { display: flex; flex-direction: row; justify-content: center; }
	html body footer div.container div.wrapper div.row:nth-child(2) a {
		margin: -15px -27.5px; padding: 17.5px;
		transform: scale(calc(1 / 3));
		width: 70px; height: 70px;
		border-radius: 5px;
		display: block; transition: calc(var(--time-tst-xfast) * 2 / 5);
	}
	html body footer div.container div.wrapper div.row:nth-child(2) a:hover {
		transform: scale(calc(1.1 / 3));
		background-color: var(--fade-black-8); box-shadow: 0px 0px var(--shd-big) var(--sys-footer-icon-bs);
	}
	html body footer div.container div.wrapper div.row:nth-child(2) a svg {
		width: inherit; height: inherit;
		cursor: pointer;
	}
	html body footer div.container div.wrapper div.row:nth-child(2) a svg path {
		stroke: var(--sys-footer-icon-color); stroke-width: 3px;
		fill: transparent;
	}
	html body footer div.container div.wrapper div.row:nth-child(2) a:nth-child(1) svg path:nth-child(3), html body footer div.container div.wrapper div.row:nth-child(2) a:nth-child(4) svg path, html body footer div.container div.wrapper div.row:nth-child(2) a:nth-child(5) svg path { stroke: none; fill: var(--sys-footer-icon-color); }
	html body footer div.container div.wrapper div.row:nth-child(2) a:nth-child(1) svg path { transform: scale(0.1625) translate(2.75px, 2.75px); stroke-width: 15px; }
	html body footer div.container div.wrapper div.row:nth-child(2) a:nth-child(4) svg path { transform: scale(0.125) translate(25px, 25px); stroke-width: 15px; }
	html body footer div.container div.wrapper div.row:nth-child(3) span a, html body footer div.container div.wrapper div.row:nth-child(3) span a:visited { color: #555; }
	html body footer div.container div.wrapper div.row:nth-child(3) span a:hover, html body footer div.container div.wrapper div.row:nth-child(3) span a:active { color: #333; }
	html body footer div.container div.wrapper div.row:nth-child(4) span {
		margin-top: 23px;
		font-family: "THKrub", serif;
		display: block;
	}
</style>
<div class="container" hidden>
	<div class="wrapper">
		<div class="row">
			<span>Maintained by Tecillium (UFDT)</span>
		</div>
		<div class="row">
			<a target="_blank" href="//line.me/R/ti/p/%40lwc0564g"><svg><path d="M77.315 0h223.133c42.523 0 77.315 34.792 77.315 77.315v223.133c0 42.523-34.792 77.315-77.315 77.315H77.315C34.792 377.764 0 342.972 0 300.448V77.315C0 34.792 34.792 0 77.315 0z" /><path d="M188.515 62.576c76.543 0 138.593 49.687 138.593 110.979 0 21.409-7.576 41.398-20.691 58.351-.649.965-1.497 2.031-2.566 3.209l-.081.088c-4.48 5.36-9.525 10.392-15.072 15.037-38.326 35.425-101.41 77.601-109.736 71.094-7.238-5.656 11.921-33.321-10.183-37.925-1.542-.177-3.08-.367-4.605-.583l-.029-.002v-.002c-64.921-9.223-114.222-54.634-114.222-109.267-.002-61.292 62.049-110.979 138.592-110.979z" /><path d="M108.103 208.954h27.952c3.976 0 7.228-3.253 7.228-7.229v-.603c0-3.976-3.252-7.228-7.228-7.228h-20.121v-45.779c0-3.976-3.252-7.228-7.228-7.228h-.603c-3.976 0-7.228 3.252-7.228 7.228v53.609c0 3.977 3.252 7.23 7.228 7.23zm173.205-33.603v-.603c0-3.976-3.253-7.228-7.229-7.228h-20.12v-11.445h20.12c3.976 0 7.229-3.252 7.229-7.228v-.603c0-3.976-3.253-7.228-7.229-7.228h-27.952c-3.976 0-7.228 3.252-7.228 7.228v53.609c0 3.976 3.252 7.229 7.228 7.229h27.952c3.976 0 7.229-3.253 7.229-7.229v-.603c0-3.976-3.253-7.228-7.229-7.228h-20.12v-11.445h20.12c3.976.002 7.229-3.251 7.229-7.226zm-53.755 31.448l.002-.003a7.207 7.207 0 0 0 2.09-5.07v-53.609c0-3.976-3.252-7.228-7.229-7.228h-.603c-3.976 0-7.228 3.252-7.228 7.228v31.469l-26.126-35.042c-1.248-2.179-3.598-3.655-6.276-3.655h-.603c-3.976 0-7.229 3.252-7.229 7.228v53.609c0 3.976 3.252 7.229 7.229 7.229h.603c3.976 0 7.228-3.253 7.228-7.229v-32.058l26.314 35.941c.162.252.339.494.53.724l.001.002c.723.986 1.712 1.662 2.814 2.075.847.35 1.773.544 2.742.544h.603a7.162 7.162 0 0 0 3.377-.844c.723-.344 1.332-.788 1.761-1.311zm-71.208 2.155h.603c3.976 0 7.228-3.253 7.228-7.229v-53.609c0-3.976-3.252-7.228-7.228-7.228h-.603c-3.976 0-7.229 3.252-7.229 7.228v53.609c0 3.976 3.253 7.229 7.229 7.229z" /></svg></a>
			<a target="_blank" href="//twitter.com/TianTcl"><svg><path d="M60.448 15.109a24.276 24.276 0 0 1-3.288.968.5.5 0 0 1-.451-.853 15.146 15.146 0 0 0 3.119-4.263.5.5 0 0 0-.677-.662 18.6 18.6 0 0 1-6.527 2.071 12.92 12.92 0 0 0-9-3.75A12.363 12.363 0 0 0 31.25 20.994a12.727 12.727 0 0 0 .281 2.719c-9.048-.274-19.61-4.647-25.781-12.249a.5.5 0 0 0-.83.073 12.475 12.475 0 0 0 2.956 14.79.5.5 0 0 1-.344.887 7.749 7.749 0 0 1-3.1-.8.5.5 0 0 0-.725.477 11.653 11.653 0 0 0 7.979 10.567.5.5 0 0 1-.09.964 12.567 12.567 0 0 1-2.834 0 .506.506 0 0 0-.536.635c.849 3.282 5.092 7.125 9.839 7.652a.5.5 0 0 1 .267.87 20.943 20.943 0 0 1-14 4.577.5.5 0 0 0-.255.942 37.29 37.29 0 0 0 17.33 4.266 34.5 34.5 0 0 0 34.687-36.182v-.469a21.11 21.11 0 0 0 4.934-4.839.5.5 0 0 0-.58-.765z" /></svg></a>
			<a target="_blank" href="//fb.me/TianTcl.net"><svg><path d="M56.375 2H7.625A5.642 5.642 0 0 0 2 7.625v48.75A5.642 5.642 0 0 0 7.625 62h26.25V37.625h-6.563V28.25h6.563v-4.687c.152-5.256 1.512-9.932 10.424-10.312h7.389v9.293h-6.247a2.953 2.953 0 0 0-3.129 2.895v2.811h9.375l-1.6 9.375h-7.775V62h14.063A5.642 5.642 0 0 0 62 56.375V7.625A5.642 5.642 0 0 0 56.375 2z" /></svg></a>
			<a target="_blank" href="//m.me/TianTcl.net"><svg><path d="m511.945312 245.640625c-1.53125-66.980469-29.011718-129.398437-77.382812-175.753906-48.300781-46.289063-111.746094-71.082031-178.589844-69.839844-66.871094-1.253906-130.285156 23.554687-178.585937 69.839844-48.371094 46.355469-75.851563 108.773437-77.38281275 175.75l-.00390625.210937v.210938c.351562 68.261718 29.582031 133.15625 80.320312 178.640625v61.886719c0 12.011718 8.386719 22.476562 19.941407 24.882812 1.710937.355469 3.433593.53125 5.148437.53125 4.648438 0 9.21875-1.296875 13.242188-3.8125l49.738281-31.070312c28.050781 9.820312 57.320313 14.796874 87.078125 14.796874h.375c1.644531.03125 3.277344.046876 4.917969.046876 65.105469-.003907 126.65625-24.707032 173.800781-69.886719 48.371094-46.355469 75.851562-108.769531 77.382812-175.75l.007813-.34375zm-98.160156 154.753906c-41.515625 39.785157-95.707031 61.539063-153.039062 61.539063-1.488282 0-2.980469-.015625-4.476563-.042969l-.164062-.003906h-.160157c-27.667968.054687-54.917968-4.753907-80.917968-14.289063-6.433594-2.359375-13.441406-1.636718-19.222656 1.976563l-45.457032 28.394531v-56.925781c0-6.347657-2.761718-12.390625-7.574218-16.578125-45.867188-39.871094-72.371094-97.578125-72.742188-158.351563 1.394531-58.898437 25.585938-113.773437 68.132812-154.546875 42.59375-40.816406 98.558594-62.644531 157.511719-61.492187l.296875.003906.296875-.003906c58.992188-1.164063 114.921875 20.675781 157.515625 61.492187 42.507813 40.738282 66.695313 95.570313 68.128906 154.414063-1.429687 58.847656-25.617187 113.675781-68.128906 154.414062zm0 0" /><path d="m394.566406 163.722656-95.316406 51.96875-57.097656-48.960937c-9.878906-8.46875-24.824219-7.894531-34.019532 1.304687l-120.511718 120.511719c-4.71875 4.75-7.308594 11.046875-7.285156 17.738281.019531 6.695313 2.644531 12.980469 7.390624 17.695313 4.839844 4.808593 11.171876 7.316406 17.609376 7.316406 4.085937 0 8.210937-1.007813 12.027343-3.078125l95.320313-51.96875 57.15625 48.980469c9.878906 8.46875 24.824218 7.894531 34.019531-1.304688l120.53125-120.53125c8.6875-8.769531 9.644531-22.804687 2.238281-32.671875-7.410156-9.914062-21.191406-12.921875-32.0625-7zm-108.683594 135.710938-57.894531-49.613282c-4.046875-3.46875-9.164062-5.257812-14.3125-5.257812-3.59375 0-7.203125.871094-10.46875 2.652344l-69.894531 38.105468 92.792969-92.792968 57.828125 49.589844c6.875 5.894531 16.835937 6.945312 24.792968 2.609374l70.066407-38.203124zm0 0" /></svg></a>
			<a target="_blank" href="//instagr.am/TianTcl"><svg><path d="M44.122 2H19.87A17.875 17.875 0 0 0 2 19.835v24.2a17.875 17.875 0 0 0 17.87 17.834h24.252A17.875 17.875 0 0 0 62 44.034v-24.2A17.875 17.875 0 0 0 44.122 2zM55.96 44.034a11.825 11.825 0 0 1-11.838 11.812H19.87A11.825 11.825 0 0 1 8.032 44.034v-24.2A11.825 11.825 0 0 1 19.87 8.022h24.252A11.825 11.825 0 0 1 55.96 19.835zm0 0" /><path d="M32 16.45a15.484 15.484 0 1 0 15.514 15.484A15.519 15.519 0 0 0 32 16.45zm0 24.95a9.461 9.461 0 1 1 9.482-9.461A9.472 9.472 0 0 1 32 41.4zm19.263-24.834a3.719 3.719 0 1 1-3.719-3.711 3.714 3.714 0 0 1 3.719 3.711zm0 0" /></svg></a>
			<a target="_blank" href="//git.io/TianTcl"><svg><path d="M32 1.952a30.019 30.019 0 0 0-9.469 58.5c1.5.281 2.063-.656 2.063-1.406v-5.625c-8.344 1.779-10.125-3.563-10.125-3.563-1.406-3.469-3.375-4.406-3.375-4.406-2.719-1.875.187-1.781.187-1.781 3 .188 4.594 3.094 4.594 3.094 2.719 4.594 7.031 3.281 8.719 2.531a6.5 6.5 0 0 1 1.875-4.031c-6.656-.75-13.688-3.375-13.688-14.812a11.5 11.5 0 0 1 3.094-8.063 11.217 11.217 0 0 1 .281-7.969s2.531-.844 8.25 3.094a28.944 28.944 0 0 1 7.5-1.031 28.4 28.4 0 0 1 7.5 1.031c5.719-3.844 8.25-3.094 8.25-3.094a11.217 11.217 0 0 1 .281 7.969 11.34 11.34 0 0 1 3.094 8.063c0 11.531-7.031 14.063-13.688 14.813a7.262 7.262 0 0 1 2.063 5.534v8.25c0 .844.562 1.687 2.063 1.406A30.019 30.019 0 0 0 32 1.952z" /></svg></a>
			<a href="tel:+66925697453"><svg><path d="M58.9 47l-10.4-6.8a4.8 4.8 0 0 0-6.5 1.3c-2.4 2.9-5.3 7.7-16.2-3.2S19.6 24.4 22.5 22a4.8 4.8 0 0 0 1.3-6.5L17 5.1c-.9-1.3-2.1-3.4-4.9-3S2 6.6 2 15.6s7.1 20 16.8 29.7S39.5 62 48.4 62s13.2-8 13.5-10-1.7-4.1-3-5z" /></svg></a>
			<a target="_blank" href="mailto:Tecillium@TianTcl.net"><svg><path d="M2 12l30 29 30-29M42 31.6L62 52M2 52l20-20.4" /><path d="M2 12h60v40H2z" /></svg></a>
		</div>
		<div class="row">
			<span><a href="//bschool.me" target="_blank">bschool.me</a> | <a href="//TianTcl.net" target="_blank">TianTcl.net</a> | <a href="//301sa.ga" target="_blank">301sa.ga</a></span>
		</div>
		<div class="row">
			<span>Since ➝ 03/06/2021</span>
		</div>
	</div>
</div><?php } ?>
<!-- New footer (BODIN) -->
<style type="text/css">
	footer > div.container {
		padding: 12.5px;
		font-size: 15px; font-weight: bold; font-family: "Kanit", "Mali", "Prompt", "TianTcl-th_01";
	}
	footer > div.container > center > u { cursor: pointer; }
	footer > div.container > .wrapper {
		margin: 0 auto 10px; padding: 10px;
		width: fit-content;
		border-radius: 5px;
		transition: var(--time-tst-fast);
	}
	footer > div.container > .wrapper:hover { background-color: var(--fade-black-8); }
	footer .credit {
		margin-bottom: 10px;
		font-size: 22.5px; text-align: center;
	}
	footer .sm-link { display: flex; justify-content: center; flex-wrap: wrap; }
	footer .sm-link a {
		margin: 1.25px 5px; padding: 5px;
		width: /*25px*/ auto; height: 25px;
		color: var(--clr-pp-deep-purple-a400) !important;
		border: 1px solid transparent; border-radius: 2.5px;
		display: block; transition: calc(var(--time-tst-xfast));
	}
	footer .sm-link a:hover {
		transform: scale(1.075);
		text-decoration: none !important;
		border-color: var(--clr-pp-green-a400);
		background-color: var(--fade-black-7);
	}
</style>
<div class="container">
	<center>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)<br>40 ซอยรามคำแหง 43/1 แขวงพลับพลา เขตวังทองหลาง กรุงเทพมหานคร 10310</center>
</div>
<hr>
<div class="container">
	<div class="wrapper" style="display: none;">
		<div class="credit">Maintained by <span onClick="app.io.warpto('/info.TianTcl.net', true)" data-title="Tecillium (UFDT)">TianTcl</span></div>
		<div class="sm-link">
			<a data-title="@lwc0564g" href="https://line.me/R/ti/p/%40lwc0564g" target="_blank">LINE</a>
			<a data-title="@TianTcl" href="https://twitter.com/TianTcl" target="_blank">Twitter</a>
			<a data-title="TianTcl.net" href="https://fb.me/TianTcl.net" target="_blank">Facebook</a>
			<a data-title="TianTcl.net" href="https://m.me/TianTcl.net" target="_blank">Messenger</a>
			<a data-title="@TianTcl" href="https://instagr.am/TianTcl" target="_blank">Instagram</a>
			<a data-title="TianTcl" href="https://git.io/TianTcl" target="_blank">Github</a>
			<a data-title="+66 925697453" href="tel:+66925697453" target="_blank">Telephone</a>
			<a data-title="TianTcl@bodin.ac.th" href="mailto:TianTcl@bodin.ac.th" target="_blank">E-mail</a>
			<a data-title="https://TianTcl.net" href="https://TianTcl.net" target="_blank">Website</a>
		</div>
	</div>
	<center>พัฒนาโดย <u onClick="$(this.parentNode.parentNode.children[0]).toggle()[0].scrollIntoView()">นายชัยณัฏฐ์ ลิ้มชุณหนุกูล</u> &emsp; &copy; 2021 - <?=date("Y")?></center>
</div>